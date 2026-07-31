<?php

declare(strict_types=1);


namespace App\Task\system;

use App\Http\Contract\Schedule\ScheduleInterface;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;

/**
 * 日程队列任务
 */
class ScheduleTask extends Task
{
    /**
     * @var Application|(Application&ScheduleInterface)|mixed|ScheduleInterface
     */
    private ScheduleInterface $schedule;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $where, private int $i, private int $limit = 500)
    {
        $this->schedule = app(ScheduleInterface::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->schedule->scheduleTimer($this->where, $this->i, $this->limit);
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
