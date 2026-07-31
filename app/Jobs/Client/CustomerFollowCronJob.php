<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 客户跟进提醒
 * Class CustomerFollowCronJob.
 */
class CustomerFollowCronJob extends CronJob
{
    /**
     * 频率：每60秒运行一次
     * @return int
     */
    public function interval()
    {
        return 60000;
    }

    public function run(): void
    {
        CustomerFollowJob::dispatch();
    }
}
