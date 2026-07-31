<?php

declare(strict_types=1);


namespace crmeb\traits;

use Illuminate\Http\Request;

/**
 * 中间件辅助
 * Trait MiddlewareTrait.
 */
trait MiddlewareTrait
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var Request
     */
    protected $request;

    /**
     * 是否初始化过.
     * @var bool
     */
    protected $initialize = false;

    /**
     * 其他参数.
     * @var array
     */
    protected $other = [];

    /**
     * 前置事件.
     * @return mixed
     */
    abstract public function before(Request $request);

    /**
     * 执行中间件.
     * @param mixed ...$args
     * @return mixed
     */
    final public function handle(Request $request, \Closure $next, ...$args)
    {
        $this->args    = $args;
        $this->request = $request;
        if (! $this->initialize) {
            $this->initialize($request);
        }
        $this->before($request);
        $response = $next($request);
        $this->after($response);
        $this->other = [];
        return $response;
    }

    /**
     * 后置中间件.
     * @param mixed $response
     * @return mixed
     */
    abstract public function after($response);

    /**
     * 获取参数.
     * @param null $default
     * @return null|mixed
     */
    protected function getArgs(int $num, $default = null)
    {
        return $this->args[$num] ?? $default;
    }

    /**
     * 初始化执行.
     */
    protected function initialize(Request $request)
    {
        $this->initialize = true;
    }
}
