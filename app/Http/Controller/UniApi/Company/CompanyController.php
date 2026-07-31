<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Company;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\Company\CompanyService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 企业控制器
 * Class CompanyController.
 */
#[Prefix('uni/company')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class CompanyController extends AuthController
{
    public function __construct(CompanyService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 当前企业用户详细信息.
     * @param mixed $user_id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('user/{user_id}', '当前企业员工详情')]
    public function userInfo(AdminService $adminService, $user_id): mixed
    {
        return $this->success($adminService->getInfo((int) $user_id, with: [
            'frames',
            'job'  => fn ($q) => $q->select('id', 'name', 'rank_id'),
            'info' => fn ($q) => $q->select('id', 'uid', 'email', 'photo'),
        ]));
    }

    /**
     * 用户权限.
     * @throws BindingResolutionException
     */
    #[Get('auth', '用户企业权限')]
    public function userAuth(): mixed
    {
        return $this->success($this->service->getUserAuth($this->entId, $this->uuid));
    }
}
