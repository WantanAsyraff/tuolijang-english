<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\ModuleEnum;
use App\Constants\System\ViewSearchEnum;
use App\Constants\UserAgentEnum;
use App\Http\Contract\Client\ClientSubscribeInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\CustomerRequest;
use App\Http\Requests\Customer\LiaisonRequest;
use App\Http\Service\Config\FormService;
use App\Http\Service\Config\SalesmanCustomService;
use App\Http\Service\Config\SystemConfigService;
use App\Http\Service\Customer\CustomerService;
use App\Http\Service\Customer\LiaisonService;
use App\Http\Service\Customer\OrderService;
use crmeb\traits\SearchTrait;
use crmeb\utils\Date;
use FormBuilder\Exception\FormBuilderException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 客户管理
 * Class CustomerController.
 */
#[Prefix('uni/client/customer')]
#[Resource('/', false, except: ['show'], names: [
    'index'   => '获取客户列表接口',
    'create'  => '保存客户表单接口',
    'store'   => '保存客户接口',
    'edit'    => '修改客户表单接口',
    'update'  => '修改客户接口',
    'destroy' => '删除客户接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class CustomerController extends AuthController
{
    use SearchTrait;

    public function __construct(CustomerService $services)
    {
        parent::__construct();
        $services->setPlatform(UserAgentEnum::UNI_AGENT);
        $this->service = $services;
    }

    /**
     * 列表数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function index(): mixed
    {
        $types                = $this->request->get('types', ViewSearchEnum::VIEW_CUSTOMER);
        $where                = $this->request->getMore(array_merge($this->service->searchField($types)));
        $where['view_search'] = (int) $this->request->get('view_search', 1);
        if ($types == ViewSearchEnum::VIEW_CUSTOMER_SEAS) {
            $where['view_search'] = 7;
        }
        if ((int) $this->request->get('is_select', 0)) {
            $uniField = ['customer_name', 'customer_tel', 'area_cascade'];
        } else {
            $uniField = ['salesman', 'customer_tel', 'customer_label', 'last_follow_time', 'customer_followed', 'customer_status', 'work_customer', 'status'];
        }
        return $this->success($this->service->getListByType($where, auth('admin')->id(), $uniField));
    }

    /**
     * 保存表单.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function create(FormService $service): mixed
    {
        $linkId       = (int) $this->request->get('link_id', 0);
        $customerForm = $service->getFormDataWithType(CustomEnum::CUSTOMER, platform: UserAgentEnum::UNI_AGENT, associationId: $linkId, linkId: $linkId);
        $liaisonForm  = $service->getFormDataWithType(CustomEnum::LIAISON, platform: UserAgentEnum::UNI_AGENT, associationId: $linkId, hidden: ['eid']);
        return $this->success(array_merge($customerForm, $liaisonForm));
    }

    /**
     * 获取订单表单是否启用订单分类.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('category_enabled', '获取订单表单是否启用订单分类')]
    public function getCategoryEnabled(): mixed
    {
        return $this->success(['enabled' => app(OrderService::class)->getCategoryEnabled()]);
    }

    /**
     * 保存.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function store(CustomerRequest $request, LiaisonRequest $liaisonRequest, LiaisonService $liaisonService): mixed
    {
        $data       = $request->validated();
        $liaison    = $liaisonRequest->setExcludeFields(['eid'])->validated();
        $customType = $this->request->post('types', ViewSearchEnum::VIEW_CUSTOMER);
        $res        = $this->service->saveCustomer($data, auth('admin')->id(), $customType, (int) $this->request->post('link_id', 0));
        $liaisonService->saveLiaison($liaison, $res->id, auth('admin')->id());
        return $this->success('common.insert.succ', ['id' => $res->id]);
    }

    /**
     * 修改.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function update(CustomerRequest $request, $id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs', ['attr' => 'id']));
        }
        $data = $request->setExcludeId((int) $id)->validated();
        $this->service->updateData($data, (int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_CUSTOMER);
        return $this->success(__('common.update.succ'));
    }

    /**
     * 获取客户基础信息.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('base/{id}', '获取客户基础信息')]
    public function baseInfo($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs', ['attr' => 'id']));
        }
        return $this->success($this->service->baseInfo((int) $id, auth('admin')->id()));
    }

    /**
     * 修改表单.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function edit($id): mixed
    {
        $userid = $this->request->get('userid', '');
        if (! $id && ! $userid) {
            return $this->fail($this->message['update']['empty']);
        }
        $id = $userid ?: (int) $id;
        return $this->success($this->service->detail($id, auth('admin')->id(), ViewSearchEnum::VIEW_CUSTOMER, ''));
    }

    /**
     * 删除.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function destroy($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->deleteCustomer((int) $id, auth('admin')->id());
        return $this->success('common.delete.succ');
    }

    /**
     * 下拉数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('select', '客户列表选择')]
    public function select(): mixed
    {
        return $this->success($this->service->getSelectList(auth('admin')->id()));
    }

    /**
     * 修改关注状态
     * @param mixed $id
     * @param mixed $status
     * @throws BindingResolutionException
     */
    #[Post('subscribe/{id}/{status}', '修改客户关注状态')]
    public function subscribe(ClientSubscribeInterface $clientSubscribeService, $id, $status): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $clientSubscribeService->subscribe(auth('admin')->id(), (int) $id, (int) $status, CustomEnum::CUSTOMER);
        return $this->success($status ? '已关注' : '已取消关注');
    }

    /**
     * 获取客户相关审批.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws FormBuilderException
     * @throws \ReflectionException
     */
    #[Get('approve/{form?}', '获取客户相关审批')]
    public function getApprove(SystemConfigService $service, int|string $form = 0): mixed
    {
        $keys = ['contract_disburse_switch', 'contract_refund_switch', 'contract_renew_switch', 'invoicing_switch', 'void_invoice_switch'];
        if ($form) {
            return $this->success($service->getApproveConfig($keys));
        }
        return $this->success($service->getApproveConfigs($keys));
    }

    /**
     * 退回公海.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('return/{id}', '退回公海')]
    public function return($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $reason = $this->request->post('reason', '');
        $this->service->returnHighSeas([$id], $reason, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 流失.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('lost/{id}', '客户流失')]
    public function lost($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }

        $this->service->lost([(int) $id], auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 领取.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('claim/{id}', '客户领取')]
    public function claim($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $this->service->claim([(int) $id], auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 取消流失.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('cancel_lost/{id}', '取消流失')]
    public function cancelLost($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->cancelLost((int) $id, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 设置标签.
     * @param mixed $id
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Post('label/{id}', '设置标签')]
    public function label($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        [$label] = $this->request->postMore([
            ['label', []],
        ], true);
        $this->service->label([$id], (array) $label);
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 客户转移.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('shift/{id}', '客户转移')]
    public function shift($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }

        [$toUid, $invoice, $contract] = $this->request->postMore([
            ['to_uid', 0],
            ['invoice', 0],
            ['contract', 0],
        ], true);
        $this->service->shift([(int) $id], (int) $toUid, (int) $invoice, (int) $contract, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 业绩简报接口.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('brief_statistics', '业绩简报')]
    public function briefStatistics(): mixed
    {
        [$time] = $this->request->getMore([
            ['time', ''],
        ], true);
        $userIds = $this->getStatisticsUserIds();
        $data    = $this->service->getBriefStatistics($time, $userIds);
        return $this->success($data);
    }

    /**
     * 业务员业绩排行接口.
     */
    #[Get('salesman_rank', '业务员业绩排行')]
    public function salesmanRank(): mixed
    {
        [$time] = $this->request->getMore([
            ['time', ''],
        ], true);
        $userIds = $this->getStatisticsUserIds();
        $data    = $this->service->getSalesmanRank($time, $userIds);
        return $this->success($data);
    }

    /**
     * 合同订单类型分析统计
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('contract_rank', '合同订单类型分析统计')]
    public function contractRank(): mixed
    {
        [$time, $categoryId] = $this->request->getMore([
            ['time', ''],
            ['category_id', 0],
        ], true);
        $userIds = $this->getStatisticsUserIds();
        $data    = $this->service->getContractRankWithNotRatio($time, $userIds, (int) $categoryId);
        return $this->success($data);
    }

    /**
     * 产品分类业绩统计.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('product_rank', '产品分类业绩统计')]
    public function productRank(OrderService $contractService): mixed
    {
        $this->withScopeFrame(module: ModuleEnum::CUSTOMER,normal:false);
        [$time, $categoryIds, $categoryId, $userIds] = $this->request->postMore([
            ['time', ''],
            ['category_id', []],
            ['category', 0],
            ['uid', []],
        ], true);

        [$searchTime, $ratioTime] = Date::ringRatioTime($time);
        $data = $contractService->getProductCategoryRank($searchTime, (array) $userIds, (array) $categoryIds, (int) $categoryId);
        return $this->success($data);
    }

    /**
     * 产品业绩排行列表（按规格维度，带分页）.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('product_rank_list', '产品业绩排行列表')]
    public function productRankList(OrderService $contractService): mixed
    {
        $this->withScopeFrame(module: ModuleEnum::CUSTOMER);
        [$time, $categoryIds, $userIds] = $this->request->postMore([
            ['time', ''],
            ['category_id', []],
            ['uid', []],
        ], true);

        [$searchTime] = Date::ringRatioTime($time);
        $data         = $contractService->getProductRankList($searchTime, (array) $userIds, (array) $categoryIds);
        return $this->success($data);
    }

    /**
     * 业务员.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('salesman', '业务员列表')]
    public function salesman(): mixed
    {
        return $this->success($this->service->getSalesman(auth('admin')->id()));
    }

    /**
     * 获取数据范围.
     */
    public function getStatisticsUserIds(): array
    {
        [$member, $type] = $this->request->getMore([
            ['member', []],
            ['type', ''],
        ], true);
        switch ($type) {
            case 0:
                $this->request->merge(['scope_frame' => 'team']);
                $this->withScopeFrame();
                $userIds = request()->get('uid');
                break;
            case 3:
                $this->request->merge(['scope_frame' => $member]);
                $this->withScopeFrame();
                $userIds = request()->get('uid');
                break;
            default:
                $userIds = $this->service->getIdsByType(auth('admin')->id(), $member, (int) $type);
        }
        return (array) $userIds;
    }

    /**
     * 获取业务数据字段接口.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('fields/{customType}', '获取业务数据字段')]
    public function getSalesmanCustom(SalesmanCustomService $service, $customType): mixed
    {
        if (! $customType) {
            return $this->fail(__('common.empty.attrs'));
        }
        return $this->success($service->salesmanCustomField(auth('admin')->id(), $customType));
    }

    protected function getRequestClassName(): string
    {
        return '';
    }

    protected function getSearchField(): array
    {
        return [];
    }

    protected function getRequestFields(): array
    {
        return [];
    }
}
