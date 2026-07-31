<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\System\ViewSearchEnum;
use App\Constants\UserAgentEnum;
use App\Http\Contract\Client\ClientSubscribeInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\OrderRequest;
use App\Http\Service\Config\FormService;
use App\Http\Service\Customer\OrderService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 订单管理
 * Class OrderController.
 */
#[Prefix('uni/client/contract')]
#[Resource('/', false, except: ['show'], names: [
    'index'   => '获取订单列表',
    'create'  => '保存订单表单',
    'store'   => '保存订单',
    'edit'    => '修改订单表单',
    'update'  => '修改订单',
    'destroy' => '删除订单',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class OrderController extends AuthController
{
    public function __construct(OrderService $services)
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
        $where                = $this->request->getMore($this->service->searchField());
        $where['view_search'] = (int) $this->request->get('view_search', 2);
        $where['types']       = ViewSearchEnum::VIEW_CONTRACT;
        return $this->success($this->service->getListByType($where, auth('admin')->id()));
    }

    /**
     * 保存表单.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function create(): mixed
    {
        $oddsId  = $this->request->get('odds_id', 0);
        $eid     = $this->request->get('eid', 0);
        $service = app()->get(FormService::class);
        return $this->success($service->getFormDataWithType(CustomEnum::CONTRACT, platform: UserAgentEnum::UNI_AGENT, associationId: (int) $eid, oddsId: (int) $oddsId));
    }

    /**
     * 保存.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function store(OrderRequest $request): mixed
    {
        $data = $request->validated();
        $res  = $this->service->saveContract($data, auth('admin')->id());
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
    public function update(OrderRequest $request, int|string $id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs', ['attr' => 'id']));
        }
        $data = $request->setExcludeId((int) $id)->validated();
        $this->service->updateContract($data, (int) $id, creatorUid: auth('admin')->id());
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
        if (! $id) {
            return $this->fail($this->message['update']['empty']);
        }
        return $this->success($this->service->detail((int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_CONTRACT, ''));
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
        $this->service->deleteContract((int) $id);
        return $this->success('common.delete.succ');
    }

    /**
     * 下拉列表.
     * @param mixed $eid
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('select/{eid}', '获取合同订单选择列表')]
    public function select($eid): mixed
    {
        if (! $eid) {
            return $this->fail('common.empty.attrs');
        }
        return $this->success($this->service->getSelectList((int) $eid, $this->uuid));
    }

    /**
     * 修改关注状态
     * @param mixed $id
     * @param mixed $status
     * @throws BindingResolutionException
     */
    #[Post('subscribe/{id}/{status}', '修改关注状态')]
    public function subscribe(ClientSubscribeInterface $clientSubscribeService, $id, $status): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $clientSubscribeService->subscribe(auth('admin')->id(), (int) $id, (int) $status, CustomEnum::CONTRACT);
        return $this->success($status ? '已关注' : '已取消关注');
    }

    /**
     * 转移.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('shift/{id}', '订单转移')]
    public function shift($id): mixed
    {
        [$toUid, $invoice] = $this->request->postMore([
            ['to_uid', 0],
            ['invoice', 0],
        ], true);
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $this->service->shift([(int) $id], (int) $toUid, (int) $invoice);
        return $this->success(__('common.operation.succ'));
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
