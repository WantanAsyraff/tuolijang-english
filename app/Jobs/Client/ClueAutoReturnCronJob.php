<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 线索自动退回.
 */
class ClueAutoReturnCronJob extends CronJob
{
    /**
     * 每60秒执行一次.
     * @return float|int
     */
    public function interval()
    {
        return 60 * 1000;
    }

    public function run(): void
    {
        ClueReturnCycleJob::dispatch();
        ClueReturnStatusJob::dispatch();
    }
}
