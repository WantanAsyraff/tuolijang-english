<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\System\ViewSearchEnum;
use App\Constants\UserAgentEnum;
use App\Http\Contract\Client\ClientSubscribeInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\OpportunityRequest;
use App\Http\Service\Config\FormService;
use App\Http\Service\Customer\OpportunityService;
use crmeb\traits\SearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 商机管理
 * Class OpportunityController.
 */
#[Prefix('uni/client/odds')]
#[Resource('/', false, except: ['show'], names: [
    'index'   => '获取商机列表接口',
    'create'  => '保存商机表单接口',
    'store'   => '保存商机接口',
    'edit'    => '修改商机表单接口',
    'update'  => '修改商机接口',
    'destroy' => '删除商机接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log', 'module.switch'])]
class OpportunityController extends AuthController
{
    use SearchTrait;

    public function __construct(OpportunityService $services)
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
    #[Post('list', '商机列表')]
    public function index(): mixed
    {
        $where = $this->request->getMore(array_merge($this->service->searchField(), [
            ['customer_label', ''],
            ['customer_status', ''],
            ['salesman_id', '', 'salesman'],
            ['view_search', ''],
            ['is_work', ''],
        ]));
        $where['odds_types'] = $where['types'] == ViewSearchEnum::VIEW_ODDS ? '' : $where['types'];
        $where['types']      = ViewSearchEnum::VIEW_ODDS;
        return $this->success($this->service->getListByType($where, auth('admin')->id(), ['eid', 'salesman', 'last_follow_time', 'status', 'followed', 'types', 'total_amount', 'work_customer', 'odds_customer', 'created_at']));
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
        return $this->success(app()->get(FormService::class)->getFormDataWithType(CustomEnum::ODDS, platform: UserAgentEnum::UNI_AGENT, associationId: (int) $this->request->get('eid', 0)));
    }

    /**
     * 保存.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function store(OpportunityRequest $request): mixed
    {
        $data = $request->validated();
        $res  = $this->service->saveOdds($data, auth('admin')->id());
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
    public function update(OpportunityRequest $request, $id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs', ['attr' => 'id']));
        }
        $data = $request->setExcludeId((int) $id)->validated();
        $this->service->updateOdds($data, (int) $id);
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
        return $this->success($this->service->detail((int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_ODDS, ''));
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
        $this->service->deleteOdds((int) $id);
        return $this->success('common.delete.succ');
    }

    /**
     * 下拉数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('select', '商机列表选择')]
    public function select(): mixed
    {
        [$eid] = $this->request->getMore([
            ['data', []],
        ], true);

        if (! $eid) {
            return $this->fail('common.empty.attrs');
        }
        return $this->success($this->service->getSelectList((array) $eid, auth('admin')->id()));
    }

    /**
     * 修改关注状态
     * @param mixed $id
     * @param mixed $status
     * @throws BindingResolutionException
     */
    #[Post('subscribe/{id}/{status}', '修改商机关注状态')]
    public function subscribe(ClientSubscribeInterface $subscribeService, $id, $status): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $subscribeService->subscribe(auth('admin')->id(), (int) $id, (int) $status, CustomEnum::ODDS);
        return $this->success($status ? '已关注' : '已取消关注');
    }

    /**
     * 商机转移.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('shift/{id}', '商机转移')]
    public function shift($id): mixed
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        [$toUid, $contract,$invoice] = $this->request->postMore([
            ['to_uid', 0],
            ['contract', 0],
            ['invoice', 0],
        ], true);
        $this->service->shift([(int) $id], auth('admin')->id(), (int) $toUid, (int) $contract, (int) $invoice);
        return $this->success(__('common.operation.succ'));
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
