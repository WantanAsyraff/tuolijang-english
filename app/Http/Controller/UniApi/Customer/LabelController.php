<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Customer\LabelService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 客户标签
 * Class LabelController.
 */
#[Prefix('uni/client/label')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class LabelController extends AuthController
{
    public function __construct(LabelService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('/', '获取客户标签列表')]
    public function index(): mixed
    {
        $where = $this->request->getMore($this->getSearchField());
        return $this->success($this->service->getList($where));
    }

    /**
     * 搜索字段.
     */
    protected function getSearchField(): array
    {
        return [
            ['name', '', 'name_like'],
            ['pid', 0],
        ];
    }
}
