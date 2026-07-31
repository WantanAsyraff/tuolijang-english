<?php

declare(strict_types=1);


namespace crmeb\swoole\server;

/**
 * Trait WithContainer.
 */
trait WithContainer
{
    /**
     * 获取配置.
     * @param null $default
     * @return mixed
     */
    public function getConfig(string $name, $default = null)
    {
        return $this->container->make('config')->get("swoole_http.{$name}", $default);
    }

    /**
     * 监听事件.
     * @param mixed $listener
     */
    public function onEvent(string $event, $listener)
    {
        $this->container->make('events')->listen("swoole.{$event}", $listener);
    }

    /**
     * 触发事件.
     * @param null $params
     */
    protected function triggerEvent(string $event, $params = null): void
    {
        $this->container->make('events')->dispatch("swoole.{$event}", $params);
    }
}
