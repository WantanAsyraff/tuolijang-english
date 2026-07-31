<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Customer;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\Customer\ProductCateRequest;
use App\Http\Service\Customer\ProductCategoryService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 产品分类.
 */
#[Prefix('ent/client/product/cate')]
#[Resource('/', false, names: [
    'index'   => '获取产品分类列表接口',
    'create'  => '获取添加产品分类接口',
    'store'   => '添加产品分类保存接口',
    'show'    => '显示隐藏产品分类接口',
    'edit'    => '获取修改产品分类接口',
    'update'  => '修改产品分类保存接口',
    'destroy' => '删除产品分类接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ProductCategoryController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(ProductCategoryService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    protected function getRequestClassName(): string
    {
        return ProductCateRequest::class;
    }

    protected function getSearchField(): array
    {
        return [
            ['name', '', 'name_like'],
            ['time', ''],
            ['status', ''],
        ];
    }

    protected function getRequestFields(): array
    {
        return [
            ['name', ''],
            ['path', []],
            ['pid', ''],
            ['uid', auth('admin')->id()],
            ['status', 1],
            ['sort', 0],
        ];
    }
}
