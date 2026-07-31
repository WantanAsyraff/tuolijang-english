<?php

declare(strict_types=1);


namespace crmeb\interfaces;

use Illuminate\Http\Request;

/**
 * 中间件接口
 * Interface MiddlewareInterface.
 */
interface ApiMiddlewareInterface
{
    /**
     * 前置.
     * @return mixed
     */
    public function before(Request $request);

    /**
     * 执行调度.
     * @param mixed ...$args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args);

    /**
     * 后置.
     * @param mixed $response
     * @return mixed
     */
    public function after($response);
}
