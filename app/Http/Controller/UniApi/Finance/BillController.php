<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Finance;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\enterprise\bill\BillRequest;
use App\Http\Service\Finance\BillService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 财务流水记录
 * Class BillController.
 */
#[Prefix('uni/finance/bill')]
#[Resource('/', false, except: ['show', 'index'], names: [
    'create'  => '获取财务流水创建接口',
    'store'   => '保存财务流水接口',
    'edit'    => '获取财务流水信息接口',
    'update'  => '修改财务流水接口',
    'show'    => '修改财务流水状态接口',
    'destroy' => '删除财务流水接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class BillController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(BillService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 财务流水列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('/list', '获取财务流水列表接口')]
    public function index()
    {
        $where = $this->request->postMore($this->getSearchField());
        return $this->success($this->service->listForUni($where));
    }

    /**
     * 财务流水列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('detail/{id}', '获取财务流水详情接口')]
    public function info($id)
    {
        return $this->success($this->service->detail($id));
    }

    /**
     * 资金统计图(总).
     * @return mixed
     * @throws BindingResolutionException
     */
    #[Post('chart', '财务流水统计图')]
    public function billTrend()
    {
        [$type, $time, $cateId] = $this->request->postMore([
            ['type', ''],
            ['time', ''],
            ['cate_id', []],
        ], true);
        $cateId = $this->service->handleCateId($cateId);
        return $this->success($this->service->trendForUni($time, 1, 1, false, $cateId, $type));
    }

    /**
     * 占比分析.
     *
     * @throws BindingResolutionException
     */
    #[Post('rank_analysis', '财务流水占比分析')]
    public function rankAnalysis(): mixed
    {
        [$time, $cateId, $types] = $this->request->postMore([
            ['time', ''],
            ['cate_id', 0],
            ['types', 1],
        ], true);

        return $this->success($this->service->getRankAnalysis($time, (int) $cateId, (int) $types));
    }

    /**
     * 搜索字段.
     *
     * @return array|string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['types', ''],
            ['cate_id', []],
            ['time', ''],
            ['type_id', ''],
            ['entid', 1],
            ['name', '', 'name_like'],
            ['sort', 'created_at'],
        ];
    }

    /**
     * 设置.
     */
    protected function getRequestClassName(): string
    {
        return BillRequest::class;
    }

    /**
     * 提取字段.
     */
    protected function getRequestFields(): array
    {
        return [
            ['cate_id', 0],
            ['types', 0],
            ['edit_time', ''],
            ['mark', ''],
            ['num', 0],
            ['type_id', 0],
            ['entid', 1],
            ['file_id', []],
            ['uid', $this->uuid],
        ];
    }
}
