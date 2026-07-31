<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\System\ViewSearchEnum;
use App\Http\Contract\Client\ClientSubscribeInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\LeadRequest;
use App\Http\Service\Config\FormService;
use App\Http\Service\Customer\LeadService;
use crmeb\traits\SearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 线索管理
 * Class CustomerClueController.
 */
#[Prefix('uni/client/clues')]
#[Resource('/', false, except: ['show', 'index'], names: [
    'create'  => '线索新增表单',
    'store'   => '新增线索',
    'edit'    => '线索修改表单',
    'update'  => '修改线索',
    'destroy' => '删除线索',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log', 'module.switch'])]
class LeadController extends AuthController
{
    use SearchTrait;

    public function __construct(LeadService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 列表数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('list', '线索列表')]
    public function index(): mixed
    {
        $types                = $this->request->post('types', ViewSearchEnum::VIEW_CLUE);
        $where                = $this->request->getMore($this->service->searchField($types));
        $where                = array_merge($where, ['repeat' => $this->request->get('repeat', '')]);
        $where['view_search'] = (int) $this->request->get('view_search', 1);
        if ($types == ViewSearchEnum::VIEW_CLUE_SEAS) {
            $where['view_search'] = 5;
        }
        return $this->success($this->service->getListByType($where, auth('admin')->id(), ['salesman', 'phone', 'source', 'last_follow_time', 'followed', 'name', 'status', 'work_customer', 'customer']));
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
        return $this->success($service->getFormDataWithType(CustomEnum::CLUE));
    }

    /**
     * 保存.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function store(LeadRequest $request): mixed
    {
        $data = $request->validated();
        $res = $this->service->saveClue($data, auth('admin')->id(), $this->request->post('types',ViewSearchEnum::VIEW_CLUE));
        return $this->success('common.insert.succ', ['id' => $res]);
    }

    /**
     * 修改.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function update(LeadRequest $request, string|int $id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $data = $request->setExcludeId((int) $id)->validated();
        $this->service->updateData($data, (int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_CLUE);
        return $this->success(__('common.update.succ'));
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
            return $this->fail(__('common.empty.attrs'));
        }
        $id   = $userid ?: (int) $id;
        return $this->success($this->service->detail($id, auth('admin')->id(), ViewSearchEnum::VIEW_CLUE, ''));
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
        $this->service->deleteClue((int) $id);
        return $this->success('common.delete.succ');
    }

    /**
     * 下拉数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('select', '线索下拉数据')]
    public function select(): mixed
    {
        return $this->success($this->service->getSelectList(auth('admin')->id()));
    }

    /**
     * 退回.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('return', '线索退回')]
    public function return(): mixed
    {
        [$data, $reason] = $this->request->postMore([
            ['data', []],
            ['reason', ''],
        ], true);

        if (empty($data) || ! is_array($data)) {
            return $this->fail('common.empty.attrs');
        }

        $this->service->returnHighSeas($data, $reason, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 修改关注状态
     * @throws BindingResolutionException
     */
    #[Post('subscribe/{id}/{status}', '修改关注状态')]
    public function subscribe($id, $status, ClientSubscribeInterface $clientSubscribeService): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $clientSubscribeService->subscribe(auth('admin')->id(), (int) $id, (int) $status, CustomEnum::CLUE);
        return $this->success($status ? '已关注' : '已取消关注');
    }

    /**
     * 业务员.
     */
    #[Get('salesman', '线索业务员')]
    public function salesman(): mixed
    {
        return $this->success($this->service->getSalesman($this->uuid));
    }

    /**
     * 领取.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('claim', '线索领取')]
    public function claim(): mixed
    {
        [$data] = $this->request->postMore([
            ['data', []],
        ], true);

        if (empty($data) || ! is_array($data)) {
            return $this->fail('common.empty.attrs');
        }

        $this->service->claim($data, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 线索转移.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('shift', '线索转移')]
    public function shift(): mixed
    {
        [$data, $toUid] = $this->request->postMore([
            ['data', []],
            ['to_uid', 0],
        ], true);
        if (! $data) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->shift((array) $data, (int) $toUid, auth('admin')->id());
        return $this->success(__('common.operation.succ'));
    }

    /**
     * 线索直接转客户.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Put('to_customer/{id}', '线索直接转客户')]
    public function toCustomer($id)
    {
        return $this->success(['id' => $this->service->toCustomer((int) $id)]);
    }

    /**
     * 设置标签.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Post('label/{id}', '设置标签')]
    public function label(mixed $id): mixed
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
}
