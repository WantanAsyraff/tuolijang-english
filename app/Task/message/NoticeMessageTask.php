<?php

declare(strict_types=1);


namespace App\Task\message;

use App\Http\Service\Notice\NoticeRecordService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 企业消息队列
 * Class MessageTask.
 */
class NoticeMessageTask extends Task
{
    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data) {}

    /**
     * @return true|void
     */
    public function handle()
    {
        try {
            app()->get(NoticeRecordService::class)->runJob($this->data);
            return true;
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
