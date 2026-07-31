<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 线索提醒事件.
 */
class ClueRemindCronJob extends CronJob
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
        ClueFollowRemindJob::dispatch();
    }
}
