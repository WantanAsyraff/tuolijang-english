<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Finance;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Finance\PaytypeService;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 支付方式
 * Class PayTypeController.
 */
#[Prefix('uni/company/pay_type')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class PayTypeController extends AuthController
{
    public function __construct(PaytypeService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     */
    #[Get('/', '获取支付方式列表')]
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
            ['ident', ''],
            ['status', 1],
            ['entid', 1],
        ]);
        $field = ['id', 'name', 'ident'];
        return $this->success($this->service->getList($where, $field, withImportTemp: false));
    }
}
