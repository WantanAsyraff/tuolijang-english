<?php

declare(strict_types=1);


namespace App\Jobs\Crud;

use App\Http\Service\Crud\SystemCrudEventService;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

/**
 * 定时执行任务
 * Class CrudEventJob.
 * @email 136327134@qq.com
 * @date 2024/4/12
 */
class CrudEventCronJob extends CronJob
{
    /**
     * @return int
     * @email 136327134@qq.com
     * @date 2024/4/12
     */
    public function interval()
    {
        return 1000; // 每1秒运行一次
    }

    /**
     * @return false
     * @email 136327134@qq.com
     * @date 2024/4/12
     */
    public function isImmediate()
    {
        return false; // 是否立即执行第一次，false则等待间隔时间后执行第一次
    }

    public function run()
    {
        try {
            app()->get(SystemCrudEventService::class)->runTimerEvent();
        } catch (\Throwable $e) {
            Log::error('实体触发器执行定时任务报错：' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }
}
