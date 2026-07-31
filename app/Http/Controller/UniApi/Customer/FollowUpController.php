<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\Customer\FollowUpRequest;
use App\Http\Service\Customer\FollowUpService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 客户跟进记录
 * Class FollowUpController.
 */
#[Prefix('uni/client/follow')]
#[Resource('/', false, except: ['create', 'show', 'edit'], names: [
    'index'   => '获取客户跟进列表',
    'store'   => '保存客户跟进',
    'update'  => '修改客户跟进',
    'destroy' => '删除客户跟进',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class FollowUpController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(FollowUpService $services)
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
            ['status', 0],
            ['view_search', ''],
            ['link_type', ''],
        ]);
        return $this->success($this->service->getList($where));
    }

    /**
     * 添加.
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function store(): mixed
    {
        $data        = $this->request->postMore($this->getRequestFields());
        $data['eid'] = $this->request->post('eid', '');
        $this->service->resourceSave($data);
        return $this->success($this->message['store']['success']);
    }

    /**
     * 按分组获取跟进记录.
     * @return mixed
     */
    #[Post('group', '按分组获取跟进记录')]
    public function group()
    {
        $where = $this->request->postMore([
            ['eid', ''],
            ['oid', ''],
            ['cid', ''],
            ['link_type', ''],
        ]);
        return $this->success($this->service->getFollowGroup($where));
    }

    protected function getRequestClassName(): string
    {
        return FollowUpRequest::class;
    }

    protected function getSearchField(): array
    {
        return [
            ['eid', ''],
            ['status', 0],
            ['link_type', ''],
        ];
    }

    protected function getRequestFields(): array
    {
        return [
            ['content', ''],
            ['attach_ids', []],
            ['types', 0],
            ['time', ''],
            ['link_type', ''],
        ];
    }
}
