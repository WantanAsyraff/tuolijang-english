<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Customer\ProductAttrValueService;
use App\Http\Service\Customer\ProductService;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 产品.
 */
#[Prefix('uni/client/products')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ProductController extends AuthController
{
    public function __construct(ProductService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取产品属性.
     * @return mixed
     */
    #[Get('attrs', '获取产品属性列表')]
    public function getAttr(ProductAttrValueService $service)
    {
        $where = $this->request->getMore([
            ['pid', ''],
            ['name', ''],
        ]);
        return $this->success($service->getList($where));
    }
}
