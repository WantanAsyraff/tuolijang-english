<?php

declare(strict_types=1);


namespace App\Jobs;

use App\Task\daily\DailyTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 日报提醒
 * Class DailyCronJob.
 */
class DailyCronJob extends CronJob
{
    /**
     * 频率：每30秒运行一次
     * @return int
     */
    public function interval()
    {
        return 30000;
    }

    /**
     * @throws BindingResolutionException
     */
    public function run(): void
    {
        Task::deliver(new DailyTask());
    }
}
