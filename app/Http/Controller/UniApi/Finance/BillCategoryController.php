<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Finance;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Finance\BillCategoryService;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 财务账目分类
 * Class RankCategoryController.
 */
#[Prefix('uni/finance/bill_cate')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class BillCategoryController extends AuthController
{
    public function __construct(BillCategoryService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     */
    #[Get('/', '获取财务账目分类列表')]
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
            ['types', ''],
        ]);
        return $this->success($this->service->getList($where));
    }
}
