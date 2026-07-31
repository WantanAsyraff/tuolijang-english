<?php

declare(strict_types=1);


namespace App\Listeners\socket;

use crmeb\socket\Ping;
use Swoole\Timer;

/**
 * WebSocket停止事件监听
 * Class WebSocketStop.
 */
class WebSocketStop
{
    /**
     * @var Ping
     */
    protected $ping;

    /**
     * Create the event listener.
     */
    public function __construct(Ping $ping)
    {
        $this->ping = $ping;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle($event)
    {
        if ($event->worker_id === 0) {
            $this->ping->destroy();
            Timer::clearAll();
        }
    }
}
