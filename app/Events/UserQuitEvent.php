<?php

declare(strict_types=1);


namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 员工离职事件
 * 用于处理员工离职后的相关业务，如销毁token、转移数据等.
 */
class UserQuitEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var int 离职用户ID
     */
    public int $userId;

    /**
     * @var int 接手人ID
     */
    public int $receiverId;

    /**
     * @var string 离职时间
     */
    public string $quitTime;

    /**
     * @param int $userId 离职用户ID
     * @param int $receiverId 接手人ID
     * @param string $quitTime 离职时间
     */
    public function __construct(int $userId, int $receiverId, string $quitTime)
    {
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->quitTime = $quitTime;
    }
}
