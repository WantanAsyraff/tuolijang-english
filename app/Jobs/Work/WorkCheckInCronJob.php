<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 同步考勤数据.
 */
class WorkCheckInCronJob extends CronJob
{
    /**
     * 频率：每100s运行一次
     */
    public function interval()
    {
        return 1000 * 100;
    }

    public function run()
    {
        WorkCheckInJob::dispatch(now()->subMinutes(2), now());
    }
}
