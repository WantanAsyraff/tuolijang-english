<?php

declare(strict_types=1);


namespace App\Task\customer;

use App\Http\Service\Customer\RemindService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 续费提醒队列
 * Class ClientBillReminderJob.
 */
class BillRemindTask extends Task
{
    public function __construct(protected int $entId, protected int $id, protected int $userId) {}

    /**
     * 执行队列.
     */
    public function handle()
    {
        try {
            app()->get(RemindService::class)->remind($this->entId, $this->id, $this->userId);
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
