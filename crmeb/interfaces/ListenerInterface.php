<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * 事件接口
 * Interface ListenerInterface.
 */
interface ListenerInterface
{
    /**
     * 执行事件.
     * @param mixed $event
     * @return mixed
     */
    public function handle($event);
}
