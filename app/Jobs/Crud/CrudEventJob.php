<?php

declare(strict_types=1);


namespace App\Jobs\Crud;

use App\Http\Service\Crud\SystemCrudEventService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 低代码触发器队列任务
 */
class CrudEventJob implements ShouldQueue
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
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            app()->get(SystemCrudEventService::class)->runTimerEvent();
        } catch (\Throwable $e) {
            Log::error('实体触发器执行定时任务报错：' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }
}
