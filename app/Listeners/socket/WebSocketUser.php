<?php

declare(strict_types=1);


namespace App\Listeners\socket;

/**
 * 用户事件处理
 * Class WebSocketUser.
 */
class WebSocketUser extends WebSocketChatBase
{
    public function login(array $data = [])
    {
        return $this->success();
    }
}
