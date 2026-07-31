<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Service\Company\CompanyService;
use App\Http\Service\System\RolesService;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 企业
 * Class AuthEnterprise.
 */
class AuthEnterprise implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function before(Request $request)
    {
        $entInfo = app()->get(CompanyService::class)->getEntInfo(1);

        $request->macro('entId', function () {
            return 1;
        });
        $request->macro('isEnt', function () {
            return true;
        });
        $request->macro('entInfo', function (?string $key = null) use ($entInfo) {
            if ($key) {
                return $entInfo[$key] ?? null;
            }
            return $entInfo;
        });
        $uri = $request->route()->uri();
        app()->get(RolesService::class)->checkAuth($uri, $request->userInfo(), $request->entInfo() ?: [], $request->method());
    }

    public function after($response)
    {
        // TODO: Implement after() method.
    }
}
