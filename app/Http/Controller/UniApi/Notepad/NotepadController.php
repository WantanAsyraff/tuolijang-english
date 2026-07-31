<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Notepad;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\user\UserMemorialRequest;
use App\Http\Service\Notepad\NotepadService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 备忘录列表控制器.
 */
#[Prefix('uni/memorials')]
#[Resource('/', false, except: ['create', 'show'], names: [
    'index'   => '备忘录列表',
    'store'   => '保存备忘录',
    'edit'    => '获取备忘录信息',
    'update'  => '修改备忘录',
    'destroy' => '删除备忘录',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class NotepadController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(NotepadService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 最新分组列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('group', '最新分组列表')]
    public function group()
    {
        $where = $this->request->getMore($this->getSearchField());
        return $this->success($this->service->getGroupList($where));
    }

    /**
     * 获取备忘录信息.
     * @return mixed
     */
    #[Get('info/{id}', '获取备忘录信息')]
    public function info($id)
    {
        return $this->success($this->service->getInfo(['id' => (int) $id, 'uid' => $this->uuid]));
    }

    /**
     * 移动端列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function index()
    {
        $where = $this->request->getMore($this->getSearchField());
        return $this->success($this->service->getListForApp($where));
    }

    /**
     * 添加.
     * @throws BindingResolutionException
     */
    public function store(): mixed
    {
        $data = $this->request()->postMore($this->getRequestFields());
        $res  = $this->service->saveData($data);
        if ($res) {
            if (is_object($res)) {
                return $this->success($this->message['store']['success'], ['id' => $res->id]);
            }

            return $this->success($this->message['store']['success'], is_array($res) ? $res : []);
        }
        return $this->fail($this->message['store']['fail']);
    }

    /**
     * 搜索字段.
     * @return array|\string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['pid', 0],
            ['name', '', 'title'],
            ['uid', $this->uuid],
        ];
    }

    /**
     * 设置.
     */
    protected function getRequestClassName(): string
    {
        return UserMemorialRequest::class;
    }

    /**
     * 提取字段.
     */
    protected function getRequestFields(): array
    {
        return [
            ['pid', 0],
            ['title', ''],
            ['content', ''],
            ['uid', $this->uuid],
        ];
    }
}
