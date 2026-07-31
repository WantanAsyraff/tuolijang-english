<?php

declare(strict_types=1);


namespace App\Http\Contract\Notice;

/**
 * 消息订阅.
 */
interface MessageSubscribeInterface
{
    /**
     * 保存取消订阅消息列表.
     */
    public function saveSubscribe(int $id, int $status, int $uid): void;

    /**
     * 验证消息是否订阅.
     */
    public function isSend(int|string $userId, int $entId, string $templateType): bool;
}
