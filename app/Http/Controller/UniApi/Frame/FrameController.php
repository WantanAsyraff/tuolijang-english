<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Frame;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Frame\FrameService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 组织架构
 * Class FrameController.
 */
#[Prefix('uni/frame')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class FrameController extends AuthController
{
    public function __construct(FrameService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取tree型组织架构数据.
     *
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    #[Get('tree', '组织架构tree型数据')]
    public function getTreeFrame(): mixed
    {
        return $this->success($this->service->getTreeFrame([], true, true));
    }

    /**
     * 管理范围.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('scope', '管理范围数据')]
    public function scopeFrame(): mixed
    {
        [$withRole, $isScope] = $this->request->getMore([
            ['role', 0],
            ['scope', 0],
        ], true);
        return $this->success($this->service->getTree(auth('admin')->id(), (bool) $withRole, (bool) $isScope));
    }
}
