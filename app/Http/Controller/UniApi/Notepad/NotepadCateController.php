<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Notepad;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\user\UserMemorialCategoryRequest;
use App\Http\Service\Notepad\NotepadCateService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 备忘录分类控制器
 * Class UserMemorialCategoryController.
 */
#[Prefix('uni/memorial/cate')]
#[Resource('/', false, except: ['show', 'create', 'edit', 'index'], names: [
    'store'   => '保存备忘录分类',
    'update'  => '修改备忘录分类',
    'destroy' => '删除备忘录分类',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class NotepadCateController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(NotepadCateService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    public function createCategory($pid = 0)
    {
        return $this->success($this->service->resourceCreate(['pid' => $pid]));
    }

    /**
     * 文件夹列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('list', '备忘录分类')]
    public function cateList()
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
            ['uid', $this->uuid],
            ['pid', 0],
            ['types', 1],
        ]);

        return $this->success($this->service->getCateList($where));
    }

    /**
     * 展示数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('tree', '备忘录树状分类')]
    public function index()
    {
        $where = $this->request->getMore($this->getSearchField());
        return $this->success($this->service->getList($where, ['*'], ['created_at' => 'desc']));
    }

    /**
     * 可移动文件夹列表.
     */
    #[Get('movable/{id}', '可移动文件夹列表')]
    public function movable($id): mixed
    {
        return $this->success($this->service->movable((int) $id, $this->uuid));
    }

    /**
     * 移动文件夹.
     * @throws BindingResolutionException
     */
    #[Post('move/{id}', '移动文件夹')]
    public function move($id): mixed
    {
        [$pid] = $this->request->postMore([
            ['pid', 0],
        ], true);

        $this->service->move((int) $id, (int) $pid, $this->uuid);
        return $this->success('common.update.succ');
    }

    protected function getRequestFields(): array
    {
        return [
            ['path', []],
            ['name', ''],
            ['pid', 0],
            ['uid', $this->uuid],
        ];
    }

    protected function getRequestClassName(): string
    {
        return UserMemorialCategoryRequest::class;
    }

    /**
     * @return array|\string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['name', '', 'name_like'],
            ['uid', $this->uuid],
            ['types', 1],
        ];
    }
}
