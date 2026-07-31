<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Constants\System\ViewSearchEnum;
use App\Constants\UserAgentEnum;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\LiaisonRequest;
use App\Http\Service\Config\FormService;
use App\Http\Service\Customer\LiaisonService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 联系人管理
 * Class LiaisonController.
 */
#[Prefix('uni/client/liaison')]
#[Resource('/', false, except: ['show'], names: [
    'index'   => '获取客户联系人列表',
    'create'  => '保存客户联系人表单',
    'store'   => '保存客户联系人',
    'edit'    => '修改客户联系人表单',
    'update'  => '修改客户联系人',
    'destroy' => '删除客户联系人',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class LiaisonController extends AuthController
{
    public function __construct(LiaisonService $services)
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
        $where = $this->request->getMore($this->service->getSearchField());
        if (isset($where['eid']) && ! $where['eid']) {
            return $this->fail('common.empty.attrs');
        }
        $where['types'] = ViewSearchEnum::VIEW_LIAISON;
        return $this->success($this->service->getListByType($where, auth('admin')->id(), ['liaison_tel', 'liaison_name', 'liaison_email', 'liaison_wechat', 'uid', 'userid', 'external_userid', 'work_customer']));
    }

    /**
     * 创建表单.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function create(): mixed
    {
        $linkId = (int) $this->request->get('link_id', 0);
        return $this->success(app(FormService::class)->getFormDataWithType(CustomEnum::LIAISON, platform: UserAgentEnum::UNI_AGENT, associationId: $linkId));
    }

    /**
     * 保存.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function store(LiaisonRequest $request): mixed
    {
        $data = $request->validated();
        $eid  = (int) $this->request->post('eid', 0);
        if ($eid < 1) {
            return $this->fail('请选择关联客户');
        }
        $res = $this->service->saveLiaison($data, $eid, auth('admin')->id());
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
    public function update(LiaisonRequest $request, int|string $id): mixed
    {
        if (! $id) {
            return $this->fail($this->message['update']['empty']);
        }
        $data = $request->setExcludeId((int) $id)->validated();
        $this->service->updateData($data, (int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_LIAISON);
        return $this->success(__('common.update.succ'));
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
        $this->service->deleteLiaison((int) $id, auth('admin')->id());
        return $this->success('common.delete.succ');
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
        return $this->success($this->service->detail((int) $id, auth('admin')->id(), ViewSearchEnum::VIEW_LIAISON));
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
