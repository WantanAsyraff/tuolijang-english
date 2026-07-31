<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Module;

use App\Constants\Crud\CrudDashboardEnum;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Crud\SystemCrudDashboardService;
use crmeb\traits\SearchTrait;
use crmeb\utils\Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * Class CrudDashboardController.
 */
#[Prefix('uni/crud/dashboard')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class CrudDashboardController extends AuthController
{
    use SearchTrait;

    public function __construct(SystemCrudDashboardService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * 获取配置.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('design/{id}', '统计看板配置')]
    public function getConfigure($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        return $this->success('ok', $this->service->getConfigure((int) $id));
    }

    /**
     * 图表数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('chart', '统计看板图表数据')]
    public function chartQuery(): mixed
    {
        $this->withScopeFrame('userId');
        $queryWhere = $this->request->postMore([
            ['type', ''],
            ['tableNameEn', ''],
            ['crudId', 0],
            ['additionalSearch', []],
            ['additionalSearchBoolean', 0],
            ['dimensionList', []],
            ['indicatorList', []],
            ['noPrivileges', 0],
            ['userId', []],
            ['uniqued', ''],
            ['search', []],
            ['chart_id', ''],
            ['chartData', ''],
        ]);
        $data = $this->service->chartQuery(Arr::snake($queryWhere));
        return $this->success($data);
    }

    /**
     * 数据列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('list', '统计看板数据列表')]
    public function listQuery(): mixed
    {
        $this->withScopeFrame('userId');
        $queryWhere = $this->request->postMore([
            ['type', CrudDashboardEnum::DATA_LIST],
            ['tableNameEn', ''],
            ['crudId', 0],
            ['systemUserId', []],
            ['showSearchType', ''],
            ['userId', []],
            ['orderBy', []],
            ['additionalSearch', []],
            ['additionalSearchBoolean', 0],
            ['keywordDefault', ''],
            ['crudId', ''],
            ['scopeFrame', ''],
            ['showField', []],
            ['page', 0],
            ['limit', 10],
            ['noPrivileges', 0],
            ['uniqued', ''],
            ['search', []],
            ['chart_id', ''],
            ['chartData', ''],
        ]);
        $data = $this->service->dataListQuery(Arr::snake($queryWhere));
        return $this->success($data);
    }
}
