<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Company;

use App\Http\Contract\Company\PromotionInterface;
use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\enterprise\trait\PromotionRequest;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 晋升表
 * Class PromotionController.
 */
#[Prefix('ent/company/promotions')]
#[Resource('/', false, except: ['create', 'edit'], names: [
    'index'   => '晋升表列表',
    'store'   => '晋升表保存',
    'show'    => '晋升表状态',
    'update'  => '晋升表修改',
    'destroy' => '晋升表删除',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class PromotionController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(PromotionInterface $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 设置Request类名.
     */
    protected function getRequestClassName(): string
    {
        return PromotionRequest::class;
    }

    /**
     * 设置请求参数获取字段.
     * @return mixed
     */
    protected function getRequestFields(): array
    {
        return [
            ['name', ''],
            ['sort', 0],
        ];
    }

    protected function getSearchField(): array
    {
        return [
            ['status', ''],
        ];
    }
}
