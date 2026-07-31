<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Service\Crud\SystemCrudService;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;

/**
 * 验证实体中间件.
 */
class CheckCrudInfo implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * 业务中间件.
     * @param Request $request
     * @throws BindingResolutionException
     */
    public function before($request)
    {
        $name = $request->route()->parameter('name');
        if ($name) {
            $request->crudInfo = app()->get(SystemCrudService::class)->getCrudInfoCache($name);
        }
    }

    public function after($response) {}
}
