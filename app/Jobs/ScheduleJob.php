<?php

declare(strict_types=1);


namespace App\Jobs;

use App\Http\Contract\Schedule\ScheduleInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 日程队列任务
 */
class ScheduleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $where, private int $i, private int $limit = 500) {}

    /**
     * Execute the job.
     */
    public function handle(ScheduleInterface $schedule): void
    {
        try {
            $schedule->scheduleTimer($this->where, $this->i, $this->limit);
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
