<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\PaymentRequest;
use App\Http\Service\Customer\PaymentService;
use crmeb\traits\SearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 续费/回款记录
 * Class ClientBillController.
 */
#[Prefix('uni/client/bill')]
#[Resource('/', false, except: ['show', 'edit', 'create'], names: [
    'index'   => '付款记录列表',
    'store'   => '保存付款记录',
    'update'  => '修改付款记录',
    'destroy' => '删除付款记录',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class PaymentController extends AuthController
{
    use SearchTrait;

    public function __construct(PaymentService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('/', '')]
    public function index(): mixed
    {
        $this->withScopeFrame('salesman_id');
        $where = $this->request->getMore([
            ['eid', ''],
            ['cid', ''],
            ['cate_id', ''],
            ['time', ''],
            ['status', ''],
            ['date', ''],
            ['name', ''],
            ['entid', 1],
            ['type_id', 0],
            ['no_withdraw', ''],
            ['invoice_id', ''],
            ['salesman_id', '', 'uid'],
            ['types', ''],
            ['sort', ['date', 'id']],
        ]);
        $field = ['id', 'eid', 'cid', 'cate_id', 'bill_cate_id', 'bill_no', 'uid', 'invoice_id', 'num', 'mark', 'date', 'end_date', 'status', 'type_id', 'pay_type', 'types', 'created_at', 'updated_at', 'apply_id'];
        return $this->success($this->service->getList($where, $field, ['date', 'id'], ['card', 'renew', 'treaty', 'cate', 'attachs' => fn ($q) => $q->select(['id', 'att_dir as src', 'relation_id', 'real_name'])]));
    }

    /**
     * 添加.
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function store(PaymentRequest $request): mixed
    {
        $request->scene(__FUNCTION__)->check();
        $data = $request->postMore([
            ['eid', 0],
            ['cid', 0],
            ['cate_id', 0],
            ['num', 0],
            ['types', ''],
            ['mark', ''],
            ['date', ''],
            ['uid', $this->uuid],
            ['type_id', 0],
            ['attach_ids', []],
            ['end_date', ''],
            ['bill_cate_id', 0],
            ['remind_id', 0],
        ]);
        $this->service->resourceSave($data);
        return $this->success('common.insert.succ');
    }

    /**
     * 修改数据.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function update($id, PaymentRequest $request): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $request->scene(__FUNCTION__)->check();
        $data = $request->postMore([
            ['cate_id', 0],
            ['num', 0],
            ['types', ''],
            ['mark', ''],
            ['date', ''],
            ['uid', $this->uuid],
            ['type_id', 0],
            ['attach_ids', []],
            ['end_date', ''],
            ['bill_cate_id', 0],
        ]);
        $this->service->resourceUpdate($id, $data);
        return $this->success('common.update.succ');
    }

    /**
     * 组合列表.
     */
    public function list(): mixed
    {
        $where = $this->request->getMore([
            ['eid', ''],
            ['cid', ''],
            ['cate_id', ''],
            ['time', ''],
            ['status', ''],
            ['field_key', ''],
            ['name', ''],
            ['entid', 1],
            ['date', ''],
        ]);
        $data = $this->service->getBillList($where);
        return $this->success($data);
    }

    /**
     * 修改备注.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('mark/{id}', '备注付款记录')]
    public function setMark($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        [$mark] = $this->request->postMore([
            ['mark', ''],
        ], true);
        $this->service->setMark($id, $mark);
        return $this->success('common.operation.succ');
    }

    /**
     * 撤回.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('withdraw/{id}', '撤回付款记录')]
    public function withdraw($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $res = $this->service->withdraw((int) $id, $this->entId);
        return $res ? $this->success('common.operation.succ') : $this->fail('common.operation.fail');
    }

    /**
     * 获取详情.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('info/{id}', '付款记录详情')]
    public function info($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $field = ['id', 'eid', 'cid', 'cate_id', 'bill_cate_id', 'uid', 'invoice_id', 'num', 'mark', 'date', 'end_date', 'status', 'type_id', 'bill_no', 'pay_type', 'types', 'fail_msg', 'created_at', 'updated_at', 'apply_id'];
        $with  = ['renew', 'card', 'treaty', 'client', 'attachs' => fn ($q) => $q->select(['id', 'att_dir as src', 'relation_id', 'real_name'])];
        return $this->success($this->service->getInfo(['id' => (int) $id, 'entid' => $this->entId], $field, $with));
    }

    /**
     * 待开票付款列表.
     */
    #[Get('invoicing', '待开票付款列表')]
    public function getUnInvoicedList(): mixed
    {
        [$eid, $invoiceId] = $this->request->getMore([
            ['eid', ''],
            ['invoice_id', ''],
        ], true);
        if (! $eid) {
            return $this->fail('common.empty.attrs');
        }
        $data = $this->service->unInvoiceList(['eid' => (int) $eid, 'invoice_id' => $invoiceId, 'entid' => $this->entId]);
        return $this->success($data, tips: 0);
    }

    /**
     * 付款记录财务审核.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('status/{id}', '付款记录财务审核')]
    public function setStatus($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $data = $this->request->postMore([
            ['status', 0],
            ['cate_id', 0],
            ['num', 0],
            ['types', ''],
            ['mark', ''],
            ['date', ''],
            ['uid', $this->uuid],
            ['type_id', 0],
            ['attach_ids', []],
            ['end_date', ''],
            ['fail_msg', ''],
            ['bill_cate_id', 0],
            ['entid', 1],
        ]);
        $res = $this->service->resourceStatusUpdate((int) $id, $data);
        return $res ? $this->success('common.operation.succ') : $this->fail('common.operation.fail');
    }

    /**
     * 财务编辑.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('finance/update/{id}', '财务编辑')]
    public function financeUpdate($id): mixed
    {
        if (! $id) {
            return $this->fail($this->message['update']['empty']);
        }
        $data = $this->request->postMore([
            ['eid', 0],
            ['cid', 0],
            ['cate_id', 0],
            ['num', 0],
            ['types', ''],
            ['mark', ''],
            ['date', ''],
            ['uid', $this->uuid],
            ['type_id', 0],
            ['attach_ids', []],
            ['end_date', ''],
            ['bill_cate_id', 0],
        ]);
        if ($this->service->resourceUpdate($id, $data, true)) {
            return $this->success(__('common.operation.succ'));
        }
        return $this->fail(__('common.operation.fail'));
    }

    /**
     * 财务删除.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Delete('finance/delete/{id}', '财务删除')]
    public function financeDelete($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $res = $this->service->financeDelete((int) $id, $this->entId);
        return $res ? $this->success('common.operation.succ') : $this->fail('common.operation.fail');
    }

    /**
     * 获取合同订单统计
     * @param mixed $cid
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('contract/statistics/{id}', '合同订单统计')]
    public function contractStatistics($cid): mixed
    {
        if (! $cid) {
            return $this->fail('common.empty.attrs');
        }
        $data = $this->service->getContractStatistics((int) $cid, $this->entId);
        return $this->success($data);
    }

    /**
     * 删除.
     * @param mixed $id
     */
    public function destroy($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        if ($this->service->resourceDelete($id)) {
            return $this->success('common.delete.succ');
        }
        return $this->fail('common.delete.fail');
    }
}
