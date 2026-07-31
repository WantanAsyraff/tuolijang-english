<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 线索即将退回提醒事件.
 */
class ClueReturnCronJob extends CronJob
{
    /**
     * 每40秒执行一次.
     * @return float|int
     */
    public function interval()
    {
        return 60 * 1000;
    }

    public function run(): void
    {
        ClueReturnRemindJob::dispatch();
    }
}
