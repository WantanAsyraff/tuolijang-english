<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Service\System\RolesService;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;

/**
 * 检查企业用户权限.
 */
class CheckRuleCompany implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * 前置.
     * @return mixed|void
     */
    public function before(Request $request)
    {
        $uri = $request->route()->uri();
        app()->get(RolesService::class)->checkAuth($uri, $request->userInfo(), $request->entInfo() ?: [], $request->method());
    }

    /**
     * 后置.
     * @return mixed|void
     */
    public function after($response)
    {
        // TODO: Implement after() method.
    }
}
