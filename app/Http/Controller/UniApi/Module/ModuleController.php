<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Module;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\AuthCrud;
use App\Http\Middleware\AuthEnterprise;
use App\Http\Middleware\CheckRuleCompany;
use App\Http\Service\Crud\CrudModuleService;
use App\Http\Service\Crud\SystemCrudCommentService;
use App\Http\Service\Crud\SystemCrudFieldService;
use App\Http\Service\Crud\SystemCrudService;
use App\Http\Service\Crud\SystemCrudTableUserService;
use App\Http\Service\System\SystemMenusService;
use crmeb\traits\SearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 移动端低代码控制器.
 */
#[Prefix('uni/crud/module')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log', 'ent.crud'])]
class ModuleController extends AuthController
{
    use SearchTrait;

    /**
     * Module constructor.
     */
    public function __construct(CrudModuleService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->middleware([
            AuthAdmin::class,
            AuthEnterprise::class,
            CheckRuleCompany::class,
            AuthCrud::class,
        ])->except(['getAssociationField', 'getAssociationList', 'saveUserTable', 'getCrudInfo']);
    }

    /**
     * 获取一对一关联展示字段.
     * @return mixed
     * @throws BindingResolutionException
     * @email 136327134@qq.com
     * @date 2024/3/12
     */
    #[Get('association_field/{id}', '一对一关联展示字段')]
    public function getAssociationField($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->service->getAssociationField((int)$id));
    }

    /**
     * 获取一对一关联展示列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @email 136327134@qq.com
     * @date 2024/3/12
     */
    #[Get('association_list/{id}', '一对一关联展示列表')]
    public function getAssociationList($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        $keyword = $this->request->get('keyword', '');

        return $this->success($this->service->getAssociationList((int)$id, (string)$keyword));
    }

    /**
     * 保存用户相关视图信息和表格展示信息.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/9
     */
    #[Post('{name}/crud', '保存用户相关视图信息和表格展示信息')]
    public function saveUserTable(SystemCrudTableUserService $service)
    {
        [$seniorSearch, $showField, $options] = $this->request->postMore([
            ['senior_search', []],
            ['show_field', []],
            ['options', []],
        ], true);

        $service->saveUserTable($this->request->crudInfo->id, auth('admin')->id(), (array)$seniorSearch, (array)$showField, (array)$options);

        return $this->success('保存成功');
    }

    /**
     * 获取实体列表字段展示和搜索字段展示.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/12
     */
    #[Get('{name}/crud/info/{id?}', '获取实体列表字段展示和搜索字段展示')]
    public function getCrudInfo()
    {
        $id = $this->request->route()->parameter('id', 0);
        return $this->success($this->service->getCrudInfo($this->request->crudInfo, auth('admin')->id(), (int)$id, true));
    }

    /**
     * 获取底部菜单.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('{name}/crud/menus', '获取底部菜单')]
    public function getCrudMenus()
    {
        $crudInfo = $this->request->crudInfo;

        $crudService = app()->get(SystemCrudService::class);
        $fieldService = app()->get(SystemCrudFieldService::class);
        $associationIds = $fieldService->crudByAssociationIds($crudInfo->id);
        // 关联表信息
        $associationTable = $crudService->getCrudList($associationIds);
        $menusCrudIds = app()->get(SystemMenusService::class)->getList(['crud_ids' => $associationIds], ['crud_id', 'uni_path', 'id', 'uni_img', 'menu_name', 'icon']);
        $menus = [];
        foreach ($menusCrudIds['list'] ?? [] as $menu) {
            foreach ($associationTable as $item) {
                if ($menu['crud_id'] === $item['id']) {
                    $menus[] = $menu;
                }
            }
        }

        $value = app()->get(SystemMenusService::class)->get(['crud_id' => $crudInfo->id], ['crud_id', 'uni_path', 'id', 'uni_img', 'menu_name', 'icon']);
        if ($menus && $value) {
            array_unshift($menus, $value->toArray());
        }

        return $this->success($menus);
    }

    /**
     * 列表展示.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/9
     */
    #[Post('{name}/list', '列表展示')]
    public function index(Request $request)
    {
        $crudInfo = $this->request->crudInfo;
        $defaultWhere['show_search_type'] = $request->post('show_search_type', '');
        //兼容前端的搜索，前端的系统搜索
        $scopeFrame = 'all';
        switch ($defaultWhere['show_search_type']) {
            case 0:
                $scopeFrame = 'all';
                break;
            case 1:
                $scopeFrame = 'dep';
                break;
            case 2:
                $scopeFrame = 'self';
                break;
        }

        $this->withScopeFrame(key: 'user_id', crudId: $request->crudInfo->id, scopeFrame: $scopeFrame);

        $systemUserId = $request->post('system_user_id', []);
        $defaultWhere['user_id'] = $request->post('user_id', []);
        $defaultWhere['uid'] = auth('admin')->id();
        $postOrderBy = $request->post('order_by', []);
        $viewSearch = $request->post('view_search', []);
        $viewSearchBoolean = $request->post('view_search_boolean', 0);
        $keywordDefault = $request->post('keyword_default');
        $crudValue = $request->post('crud_value', 0);
        $crudId = $request->post('crud_id', 0);

        if ($systemUserId && $request->post('scope_frame') === 'all') {
            $defaultWhere['user_id'] = array_merge($defaultWhere['user_id'], $systemUserId);
        }

        $orderBy = [];
        if ($postOrderBy) {
            $orderByField = $this->service->getOrderByField($crudInfo->id);
            foreach ($orderByField as $item) {
                if (isset($postOrderBy[$item['field_name_en']])) {
                    $orderBy[$item['field_name_en']] = $postOrderBy[$item['field_name_en']] ? 'desc' : 'asc';
                }
            }
        }

        if (isset($postOrderBy['default_field_name_en'])) {
            $orderBy = [];
        }

        $viewNewSearch = [];
        foreach ($viewSearch as $search) {
            if ($search['form_field_uniqid']) {
                $viewNewSearch[] = [
                    'field_name' => $search['form_field_uniqid'],
                    'operator'   => $search['operator'],
                    'value'      => $search['value'] ?? '',
                ];
            }
        }
        $viewSearch = $viewNewSearch;

        return $this->success($this->service->getModuleList($request, $crudInfo, $defaultWhere, $orderBy, (array)$viewSearch, (string)$keywordDefault, (int)$viewSearchBoolean, (int)$crudId, (int)$crudValue, true));
    }

    /**
     * 获取新建页面的表单信息.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/12
     */
    #[Get('{name}/create', '新建页面的表单')]
    public function create(Request $request)
    {
        $crudId = $request->get('crud_id', 0);
        $crudValue = $request->get('crud_value', 0);
        $id = $request->get('id', 0);

        return $this->success($this->service->getCreateForm($this->request->crudInfo, $crudId, $crudValue, (int)$id, true));
    }

    /**
     * 保存数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/13
     */
    #[Post('{name}/save', '保存数据')]
    public function save(Request $request)
    {
        $crudInfo = $this->request->crudInfo;

        if (in_array($crudInfo->table_name_en, app()->get(SystemCrudService::class)->notAllowOperateTable())) {
            return $this->fail('系统默认数据不允许创建');
        }

        $crudId = $request->post('crud_id', 0);
        $crudValue = $request->post('crud_value', 0);

        $data = $this->service->checkData($crudInfo, $request);

        $this->service->saveModule($crudInfo, $data, [
            'uid' => auth('admin')->id(),
        ], (int)$crudId, (int)$crudValue);

        return $this->success('添加成功');
    }

    /**
     * 更新数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/13
     */
    #[Put('{name}/update/{id}', '更新数据')]
    public function update(Request $request)
    {
        $id = $this->request->route()->parameter('id', 0);
        $crudInfo = $this->request->crudInfo;
        if (in_array($crudInfo->table_name_en, app()->get(SystemCrudService::class)->notAllowOperateTable())) {
            return $this->fail('系统默认数据不允许更新');
        }

        if (!$id) {
            return $this->fail('缺少参数');
        }

        $data = $this->service->checkData($crudInfo, $request);

        $this->service->updateModule($crudInfo, (int)$id, $data, [
            'uid' => auth('admin')->id(),
        ]);

        return $this->success('修改成功');
    }

    /**
     * 获取实体数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/14
     */
    #[Get('{name}/find/{id}', '获取实体数据')]
    public function find()
    {
        $id = $this->request->route()->parameter('id', 0);

        if (!$id) {
            return $this->fail('缺少参数');
        }

        return $this->success($this->service->getFindUniModule($this->request->crudInfo, (int)$id));
    }

    /**
     * 删除数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/3/14
     */
    #[Delete('{name}/delete/{id}', '删除数据')]
    public function delete()
    {
        $id = $this->request->route()->parameter('id', 0);
        $crudInfo = $this->request->crudInfo;

        if (in_array($crudInfo->table_name_en, app()->get(SystemCrudService::class)->notAllowOperateTable())) {
            return $this->fail('系统默认数据不允许删除');
        }

        if (!$id) {
            return $this->fail('缺少参数');
        }

        $systemUserIds = $this->request->post('system_user_id', []);
        $this->service->deleteModule($crudInfo, (int)$id, (array)$systemUserIds);

        return $this->success('删除成功');
    }

    /**
     * 评论.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Post('{name}/comment/{id}', '低代码评论')]
    public function saveComment(Request $request, SystemCrudCommentService $commentService)
    {
        $id = $this->request->route()->parameter('id', 0);
        $crudInfo = $this->request->crudInfo;
        $data = $request->postMore([
            ['pid', 0],
            ['comment', ''],
        ]);

        if (!$id) {
            return $this->fail('缺少参数');
        }
        if (!$data['comment']) {
            return $this->fail('请输入评论内容');
        }

        $commentService->createComment($crudInfo, $data, (int)$id, auth('admin')->id());

        return $this->success('评论成功');
    }

    /**
     * 修改评论.
     * @return mixed
     */
    #[Put('{name}/comment/{comment_id}', '低代码修改评论')]
    public function updateComment(Request $request, SystemCrudCommentService $commentService)
    {
        $id = $this->request->route()->parameter('comment_id', 0);
        $data = $request->postMore([
            ['comment', ''],
        ]);

        if (!$id) {
            return $this->fail('缺少参数');
        }
        if (!$data['comment']) {
            return $this->fail('请输入评论内容');
        }

        $comment = $commentService->get($id);
        if (!$comment) {
            return $this->fail('评论不存在');
        }

        if ($comment->uid != auth('admin')->id()) {
            return $this->fail('只能修改自己的评论');
        }

        $comment->comment = $data['comment'];
        $comment->save();

        return $this->success('修改成功');
    }

    /**
     * 删除评论.
     * @return mixed
     */
    #[Delete('{name}/comment/{comment_id}', '低代码删除评论')]
    public function deleteComment(SystemCrudCommentService $commentService)
    {
        $id = $this->request->route()->parameter('comment_id', 0);

        if (!$id) {
            return $this->fail('缺少参数');
        }

        $comment = $commentService->get($id);
        if (!$comment) {
            return $this->fail('评论不存在');
        }

        if ($comment->uid != auth('admin')->id()) {
            return $this->fail('只能删除自己的评论');
        }

        // 删除下级的所有评论
        if ($commentService->exists(['pid' => $comment->id])) {
            $commentService->delete(['pid' => $comment->id]);
        }

        $comment->delete();

        return $this->success('删除成功');
    }

    /**
     * 获取评论列表.
     * @return mixed
     */
    #[Get('{name}/comment/{id}', '低代码获取评论列表')]
    public function getCommentList(SystemCrudCommentService $commentService)
    {
        $id = $this->request->route()->parameter('id', 0);
        if (!$id) {
            return $this->fail('缺少参数');
        }

        return $this->success($commentService->getCommentList($this->request->crudInfo, (int)$id));
    }
}
