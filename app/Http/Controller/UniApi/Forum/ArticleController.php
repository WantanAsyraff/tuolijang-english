<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Forum;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\System\MenusService;
use crmeb\services\synchro\Article;
use crmeb\services\synchro\CompanySms;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 知识社区.
 * Class ArticleController.
 */
#[Prefix('uni/article')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ArticleController extends AuthController
{
    public function __construct(Article $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 文章用户修改密码
     * @throws BindingResolutionException
     */
    #[Post('user/save', '修改文章管理密码')]
    public function editPwd()
    {
        $data = $this->request->postMore([
            ['phone', ''],
            ['password', ''],
            ['captcha', ''],
        ]);
        $this->service->setFromType($this->uuid)->saveUser($data);
        return $this->success('密码修改成功');
    }

    /**
     * 文章用户修改密码
     * @return mixed
     * @throws BindingResolutionException|InvalidArgumentException
     */
    #[Post('user/login', '登录文章管理账号')]
    public function login()
    {
        $data = $this->request->postMore([
            ['phone', ''],
            ['captcha', ''],
        ]);
        return $this->success($this->service->setFromType($this->uuid)->articleLogin($data));
    }

    /**
     * 修改文章推荐标签.
     * @param mixed $types
     * @return mixed
     * @throws InvalidArgumentException
     */
    #[Get('label/{types}', '获取文章分类')]
    public function label(MenusService $service, $types = 0)
    {
        $menus = $service->getMenusForUser($this->uuid, $this->entId, false, 'menu_path');
        return $this->success($this->service->setFromType($this->uuid)->articleCate($types, $menus));
    }

    /**
     * 保存用户标签.
     * @return mixed
     * @throws InvalidArgumentException
     */
    #[Post('label', '保存用户文章标签')]
    public function saveLabel()
    {
        $data = $this->request->postMore([
            ['label', []],
        ]);
        if (! $data['label']) {
            return $this->fail('请选择标签');
        }
        return $this->success($this->service->setFromType($this->uuid)->articleLabelSave($data));
    }

    /**
     * 获取用户标签.
     * @return mixed
     * @throws InvalidArgumentException
     */
    #[Get('user_label', '获取用户文章标签')]
    public function getLabel()
    {
        return $this->success($this->service->setFromType($this->uuid)->articleLabelGet());
    }

    /**
     * 获取文章列表.
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Post('list', '获取文章列表')]
    public function list()
    {
        $data = $this->request->postMore([
            ['sort', ''],
            ['page', 1],
            ['limit', 20],
            ['name', ''],
            ['label_id', ''],
            ['status', 0],
            ['draft', 0],
            ['menus', app()->get(MenusService::class)->getMenusForUser($this->uuid, $this->entId, false, 'menu_path')],
        ]);
        return $this->success($this->service->setFromType($this->uuid)->articleList($data));
    }

    /**
     * 获取文章详情.
     * @param mixed $id
     * @throws InvalidArgumentException
     */
    #[Get('info/{id}', '获取文章详情')]
    public function info($id): mixed
    {
        if (! $id) {
            return $this->fail('缺少文章ID');
        }
        return $this->success($this->service->setFromType($this->uuid)->articleInfo($id));
    }

    /**
     * 保存文章信息.
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['cid', 0],
            ['label', []],
            ['title', ''],
            ['info', ''],
            ['author', ''],
            ['image', ''],
            ['url', ''],
            ['types', ''],
            ['content', ''],
            ['draft', 0],
        ]);
        return $this->success($this->service->setFromType($this->uuid)->articleSave($data));
    }

    /**
     * 收藏文章.
     * @throws BindingResolutionException|InvalidArgumentException
     */
    #[Post('collect', '收藏/取消收藏文章')]
    public function collect(): mixed
    {
        [$id, $status] = $this->request->postMore([
            ['id', 0],
            ['status', 1],
        ], true);
        if (! $id) {
            return $this->fail('缺少文章ID');
        }
        return $this->success($this->service->setFromType($this->uuid)->articleCollect((int) $id, $status));
    }

    /**
     * 点赞文章.
     * @throws BindingResolutionException|InvalidArgumentException
     */
    #[Post('support', '点赞/取消点赞文章')]
    public function support(): mixed
    {
        [$id, $status] = $this->request->postMore([
            ['id', 0],
            ['status', 1],
        ], true);
        if (! $id) {
            return $this->fail('缺少文章ID');
        }
        return $this->success($this->service->setFromType($this->uuid)->articleSupport((int) $id, $status));
    }

    /**
     * 获取公共验证码
     * @param mixed $phone
     */
    #[Get('captcha/{phone}', '获取公共验证码')]
    public function captcha(CompanySms $services, $phone): mixed
    {
        return $this->success($services->commonCaptcha($phone));
    }
}
