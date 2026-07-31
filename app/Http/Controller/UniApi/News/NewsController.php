<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\News;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\enterprise\notice\NoticeRequest;
use App\Http\Service\News\NewsCateService;
use App\Http\Service\News\NewsService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 企业控制器
 * Class NewsController.
 */
#[Prefix('uni/news')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class NewsController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(NewsService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取详情.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('detail/{id}', '通知公告详情')]
    public function getInfo($id): mixed
    {
        return $this->success($this->service->getInfo($id));
    }

    /**
     * 获取日期分组列表详情.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('list', '日期分组列表详情')]
    public function group(): mixed
    {
        $where = $this->request->getMore($this->getSearchField());
        return $this->success($this->service->getGroupList($where));
    }

    /**
     * 通知分类.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('cate', '通知分类')]
    public function cate(NewsCateService $service): mixed
    {
        return $this->success($service->getList([]));
    }

    /**
     * 搜索字段.
     * @return array|\string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['cate_id', ''],
            ['title', '', 'title_like'],
            ['status', ''],
            ['is_new', ''],
            ['time', ''],
            ['entid', 1],
        ];
    }

    /**
     * 设置过滤.
     */
    protected function getRequestClassName(): string
    {
        return NoticeRequest::class;
    }

    /**
     * 提取字段.
     */
    protected function getRequestFields(): array
    {
        return [
            ['cate_id', 0],
            ['title', ''],
            ['cover', ''],
            ['info', ''],
            ['content', ''],
            ['is_top', 0],
            ['push_type', 0],
            ['push_time', ''],
            ['sort', 0],
            ['entid', 1],
        ];
    }
}
