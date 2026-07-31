<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\InvoiceRequest;
use App\Http\Service\Customer\InvoiceService;
use crmeb\traits\SearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 客户发票
 * Class InvoiceController.
 */
#[Prefix('uni/client/invoice')]
#[Resource('/', false, except: ['show', 'edit', 'create', 'destroy'], names: [
    'index'  => '获取发票列表',
    'store'  => '保存发票',
    'update' => '修改发票',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log', 'module.switch'])]
class InvoiceController extends AuthController
{
    use SearchTrait;

    public function __construct(InvoiceService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('list', '财务获取发票列表')]
    public function list(): mixed
    {
        $this->withScopeFrame();
        $where = $this->request->getMore([
            ['types', ''],
            ['status', [1, -1, 4, 5]],
            ['name', '', 'name_like'],
            ['date', '', 'real_date'],
            ['time', '', 'created_at'],
            ['uid', []],
        ]);
        $field = ['id', 'eid', 'uid', 'cid', 'name', 'title', 'ident', 'bank', 'types', 'num', 'price', 'amount', 'account', 'address', 'tel', 'collect_name', 'collect_tel', 'collect_email', 'mail_address', 'invoice_type', 'invoice_address', 'bill_date', 'mark', 'remark', 'card_remark', 'finance_remark', 'collect_type', 'real_date', 'status', 'created_at', 'updated_at'];
        return $this->success($this->service->listForFinance($where, $field, with: ['card', 'treaty']));
    }

    /**
     * 展示数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['types', ''],
            ['status', ''],
            ['invoiced', ''],
            ['eid', ''],
            ['cid', ''],
            ['name', '', 'name_like'],
            ['time', '', 'time_data'],
            ['from', ''],
            ['category_id', ''],
            ['entid', 1],
            ['uid', auth('admin')->id()],
            ['time_field', 'time'],
            ['view_search', 1],
            ['scope_frame', ''],
        ]);
        if ($where['cid'] || $where['eid']) {
            unset($where['view_search']);
        }
        if (! $where['time_field']) {
            $where['time_field'] = 'time';
        }
        $field = ['id', 'eid', 'uid', 'cid', 'name', 'title', 'ident', 'bank', 'types', 'num', 'price', 'amount', 'account', 'address', 'tel', 'collect_name', 'collect_tel', 'collect_email', 'mail_address', 'invoice_type', 'invoice_address', 'bill_date', 'mark', 'remark', 'card_remark', 'finance_remark', 'collect_type', 'real_date', 'status', 'created_at', 'updated_at'];
        return $this->success($this->service->getList($where, $field, with: ['card', 'treaty'], uid: auth('admin')->id()));
    }

    /**
     * 添加.
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function store(InvoiceRequest $request): mixed
    {
        $request->scene(__FUNCTION__)->check();
        $data = $request->postMore([
            ['eid', 0],
            ['cid', 0],
            ['name', ''],
            ['amount', 0],
            ['types', ''],
            ['title', ''],
            ['ident', ''],
            ['bank', ''],
            ['account', ''],
            ['address', ''],
            ['tel', ''],
            ['collect_name', ''],
            ['collect_tel', ''],
            ['collect_type', ''],
            ['collect_email', ''],
            ['bill_date', ''],
            ['mark', ''],
            ['category_id', 0],
            ['bill_id', []],
            ['mail_address', ''],
        ]);
        $this->service->resourceSave($data);
        return $this->success('common.insert.succ');
    }

    /**
     * 修改数据.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function update($id, InvoiceRequest $request): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $request->scene(__FUNCTION__)->check();
        $data = $request->postMore([
            ['name', ''],
            ['amount', 0],
            ['types', ''],
            ['title', ''],
            ['ident', ''],
            ['bank', ''],
            ['account', ''],
            ['address', ''],
            ['tel', ''],
            ['collect_name', ''],
            ['collect_tel', ''],
            ['collect_type', ''],
            ['collect_email', ''],
            ['bill_date', ''],
            ['mark', ''],
            ['category_id', 0],
            ['bill_id', []],
            ['mail_address', ''],
        ]);
        $this->service->resourceUpdate($id, $data);
        return $this->success('common.update.succ');
    }

    /**
     * 付款记录财务审核.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('status/{id}', '财务审核发票')]
    public function setStatus($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $data = $this->request->postMore([
            ['remark', ''],
            ['attach_ids', []],
            ['is_send', 0],
            ['invoice_type', ''],
            ['invoice_address', ''],
            ['invoice_mail', ''],
            ['collect_name', ''],
            ['collect_tel', ''],
        ]);
        $this->service->resourceStatusUpdate((int) $id, $data, (int) $this->request->post('status', 0));
        return $this->success('common.operation.succ');
    }

    /**
     * 修改备注.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('mark/{id}', '修改备注')]
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
     * 客户发票转移.
     * @throws BindingResolutionException|\ReflectionException
     */
    #[Post('shift', '客户发票转移')]
    public function shift(): mixed
    {
        [$ids, $toUid] = $this->request->postMore([
            ['ids', []],
            ['to_uid', 0],
        ], true);
        if (! $ids) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->shift($ids, $toUid);
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 作废申请.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('invalid_apply/{id}', '发票作废申请')]
    public function invalidApply($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        [$invalid, $remark] = $this->request->postMore([
            ['invalid', 1],
            ['remark', ''],
        ], true);

        if (! in_array($invalid, [-1, 1])) {
            return $this->fail('参数错误');
        }
        $this->service->invalidApply((int) $id, (int) $invalid, $remark);
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 作废审核.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('invalid/{id}', '发票作废审核')]
    public function invalidReview($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        [$invalid, $remark] = $this->request->postMore([
            ['invalid', 2],
            ['remark', ''],
        ], true);

        if (! in_array($invalid, [2, 3])) {
            return $this->fail('参数错误');
        }
        $this->service->invalidReview((int) $id, auth('admin')->id(), (int) $invalid, $remark);
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 获取累计/审核中/付款金额.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getPriceStatistics(): mixed
    {
        [$eid, $cid] = $this->request->getMore([
            ['eid', ''],
            ['cid', ''],
        ], true);
        if (! $eid && ! $cid) {
            return $this->fail('common.empty.attrs');
        }
        $data = $this->service->priceStatistics($this->entId, $eid, $cid);
        return $this->success($data);
    }

    /**
     * 开票撤回.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('withdraw/{id}', '发票撤回')]
    public function withdraw($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        [$remark] = $this->request->postMore([
            ['remark', ''],
        ], true);
        $res = $this->service->withdraw($id, $remark);
        return $res ? $this->success('common.operation.succ') : $this->fail('common.operation.fail');
    }

    /**
     * 获取详情.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('info/{id}', '客户发票详情')]
    public function info($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $with = ['card', 'treaty', 'client', 'attachs', 'category'];
        return $this->success($this->service->getInfo(['id' => (int) $id, 'entid' => $this->entId], with: $with));
    }

    /**
     * 删除.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function destroy($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->resourceDelete($id);
        return $this->success('common.delete.succ');
    }

    /**
     * 获取发票类目.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('category', '获取发票类目')]
    public function cate(): mixed
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
        ]);
        return $this->success($this->service->getList($where, ['id', 'name']));
    }
}
