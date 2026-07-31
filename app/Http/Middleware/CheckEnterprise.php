<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use crmeb\exceptions\EntException;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;

/**
 * 检测企业是否存在
 * Class CheckEnterprise.
 */
class CheckEnterprise implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * 前置事件.
     * @return mixed
     */
    public function before(Request $request)
    {
        if (! $request->hasMacro('isEnt') || ! $request->hasMacro('entInfo')) {
            throw new EntException('您没有权限操作', 410005);
        }
        if (! $request->entInfo()) {
            throw new EntException('企业不存在', 410005);
        }
        if (! $request->entInfo('status')) {
            throw new EntException('企业已被禁用无法使用', 410005);
        }
    }

    /**
     * 后置中间件.
     * @return mixed
     */
    public function after($response)
    {
        // TODO: Implement after() method.
    }
}
