<?php

declare(strict_types=1);


namespace App\Task\daily;

use App\Http\Service\Admin\AdminService;
use App\Http\Service\Message\MessageService;
use App\Http\Service\Report\ReportService;
use crmeb\utils\MessageType;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 日报事件
 * Class DailyTask.
 */
class DailyTask extends Task
{
    protected int $limit = 50;

    public function __construct() {}

    public function handle(): void
    {
        try {
            $message = app()->get(MessageService::class)->getMessageContent(1, MessageType::DAILY_REMIND_TYPE);
            if ($message && ! empty($message['remind_time']) && date('H:i') == $message['remind_time']) {
                $count = ceil(app()->get(AdminService::class)->count(['status' => 1]) / $this->limit);
                if ($count < 1) {
                    return;
                }
                $dailyService = app()->get(ReportService::class);
                for ($i = 1; $i <= $count; ++$i) {
                    $dailyService->timer($i, $this->limit);
                }
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
