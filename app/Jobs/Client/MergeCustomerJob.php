<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\System\ViewSearchEnum;
use App\Http\Service\Attach\AttachService;
use App\Http\Service\Customer\PaymentService;
use App\Http\Service\Customer\FollowUpService;
use App\Http\Service\Customer\InvoiceService;
use App\Http\Service\Customer\SubscribeService;
use App\Http\Service\Customer\OrderService;
use App\Http\Service\Customer\LeadService;
use App\Http\Service\Customer\LiaisonService;
use App\Http\Service\Customer\OpportunityService;
use App\Http\Service\Customer\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 合并企微关联客户.
 */
class MergeCustomerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    /**
     * @var CustomerService|mixed
     */
    private CustomerService $customerService;

    /**
     * @var OrderService|mixed
     */
    private OrderService $contractService;

    /**
     * @var FollowUpService|mixed
     */
    private FollowUpService $clientFollowService;

    /**
     * @var InvoiceService|mixed
     */
    private InvoiceService $invoiceService;

    /**
     * @var OpportunityService|mixed
     */
    private OpportunityService $oddsService;

    /**
     * @var AttachService|mixed
     */
    private AttachService $attachService;

    /**
     * @var PaymentService|mixed
     */
    private PaymentService $billService;

    /**
     * @var LiaisonService|mixed
     */
    private LiaisonService $liaisonService;

    /**
     * @var SubscribeService|mixed
     */
    private SubscribeService $subscribeService;

    private LeadService $clueService;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $externalUserid, protected int $mainId = 0)
    {
        $this->customerService     = app(CustomerService::class);
        $this->contractService     = app(OrderService::class);
        $this->clientFollowService = app(FollowUpService::class);
        $this->invoiceService      = app(InvoiceService::class);
        $this->oddsService         = app(OpportunityService::class);
        $this->attachService       = app(AttachService::class);
        $this->billService         = app(PaymentService::class);
        $this->liaisonService      = app(LiaisonService::class);
        $this->subscribeService    = app(SubscribeService::class);
        $this->clueService         = app(LeadService::class);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            if (! $this->externalUserid){
                return;
            }
            $clueMember = array_unique($this->clueService->column(['external_userid' => $this->externalUserid], 'uid'));
            $customer   = $this->customerService->select(['external_userid' => $this->externalUserid], ['id', 'uid', 'external_userid', 'userid', 'customer_status', 'customer_name', 'member'])?->toArray();
            if (! $customer || count($customer) === 1) {
                return;
            }
            $normalCount = count(array_filter(array_column($customer, 'customer_status'), function ($item) {
                return $item == 1;
            }));
            $failCount = count(array_filter(array_column($customer, 'customer_status'), function ($item) {
                return $item == 0;
            }));
            if ($this->mainId) {
                $data = array_filter($customer, function ($item) {
                    return $item['id'] == $this->mainId;
                });
                $firstCustomer = end($data);
                $customer      = array_filter($customer, function ($item) use ($firstCustomer) {
                    return $item['id'] != $firstCustomer['id'];
                });
            } else {
                $firstCustomer = array_shift($customer);
            }
            if ($normalCount === count($customer)) {
                $status = 1;
            } elseif ($failCount === count($customer)) {
                $status = 0;
            } else {
                $status = $firstCustomer['customer_status'];
            }
            DB::transaction(function () use ($customer, $firstCustomer, $status, $clueMember) {
                // 客户修改
                $member = array_values(array_unique(array_merge($firstCustomer['member'] ?: [], array_merge(...array_column($customer, 'member') ?: []) ?: [])));
                $member = array_unique(array_merge($member, $clueMember));
                $userId = array_values(array_filter(array_column($customer, 'uid'), function ($item) use ($firstCustomer) {
                    return $item != $firstCustomer['uid'];
                }));
                $member = array_values(array_unique(array_merge($member, $userId))) ?: null;
                // 删除线索
                $this->clueService->delete(['external_userid' => $this->externalUserid]);
                $this->customerService->update($firstCustomer['id'], ['customer_status' => $status, 'member' => $member]);
                // 订单
                $this->contractService->update(['eid' => array_column($customer, 'id')], ['eid' => $firstCustomer['id']]);
                // 关注
                $this->subscribeService->update(['eid' => array_column($customer, 'id'), 'types' => CustomEnum::CUSTOMER], ['eid' => $firstCustomer['id']]);
                // 发票
                $this->invoiceService->update(['eid' => array_column($customer, 'id')], ['eid' => $firstCustomer['id']]);
                // 联系人
                $this->liaisonService->update(['eid' => array_column($customer, 'id')], ['eid' => $firstCustomer['id']]);
                // 商机
                $this->oddsService->update(['eid' => array_column($customer, 'id')], ['eid' => $firstCustomer['id']]);
                // 资金
                $this->billService->update(['eid' => array_column($customer, 'id')], ['eid' => $firstCustomer['id']]);
                // 附件
                $this->attachService->update(['relation_type' => ViewSearchEnum::VIEW_CUSTOMER, 'relation_id' => array_column($customer, 'id')], ['relation_id' => $firstCustomer['id']]);
                // 客户跟进
                $this->clientFollowService->update(['eid' => array_column($customer, 'id'), 'link_type' => ViewSearchEnum::VIEW_CUSTOMER], ['eid' => $firstCustomer['id']]);
                // 删除其他客户
                $this->customerService->delete(['id' => array_column($customer, 'id')]);
            });
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
