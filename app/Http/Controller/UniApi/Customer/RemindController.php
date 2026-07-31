<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\RemindRequest;
use App\Http\Service\Customer\RemindService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 付费提醒
 * Class RemindController.
 */
#[Prefix('uni/client/remind')]
#[Resource('/', false, except: ['create', 'show', 'edit'], names: [
    'index'   => '获取付款提醒列表',
    'store'   => '保存付款提醒',
    'update'  => '修改付款提醒',
    'destroy' => '删除付款提醒',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class RemindController extends AuthController
{
    public function __construct(RemindService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     */
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['eid', ''],
            ['cid', ''],
            ['types', ''],
        ]);
        $field = ['id', 'eid', 'cid', 'cate_id', 'user_id', 'num', 'mark', 'time', 'rate', 'bill_id', 'status', 'period', 'types', 'created_at', 'updated_at'];
        return $this->success($this->service->getList($where, $field, with: ['card', 'renew']));
    }

    /**
     * 添加.
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function store(RemindRequest $request): mixed
    {
        $request->scene(__FUNCTION__)->check();

        $data = $request->postMore([
            ['eid', ''],
            ['cid', ''],
            ['num', ''],
            ['mark', ''],
            ['types', 0],
            ['time', ''],
            ['rate', ''],
            ['period', ''],
            ['cate_id', ''],
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
    public function update($id, RemindRequest $request): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $request->scene(__FUNCTION__)->check();
        $data = $request->postMore([
            ['num', ''],
            ['mark', ''],
            ['types', 0],
            ['time', ''],
            ['rate', ''],
            ['period', ''],
            ['cate_id', ''],
        ]);
        $this->service->resourceUpdate($id, $data);
        return $this->success('common.update.succ');
    }

    /**
     * 删除数据.
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
     * 获取详情.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('info/{id}', '获取付款提醒详情')]
    public function info($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $field = ['id', 'eid', 'cid', 'cate_id', 'user_id', 'num', 'mark', 'time', 'rate', 'period', 'types', 'created_at', 'updated_at'];
        return $this->success($this->service->getInfo(['id' => (int) $id], $field));
    }

    /**
     * 放弃.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('abjure/{id}', '付款放弃')]
    public function abjure($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }

        $this->service->updateStatus((int) $id, 1);
        return $this->success('common.operation.succ');
    }
}
