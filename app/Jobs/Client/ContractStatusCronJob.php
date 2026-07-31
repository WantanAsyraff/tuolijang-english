<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 订单状态定时任务
 */
class ContractStatusCronJob extends CronJob
{
    /**
     * 频率：每5m运行一次
     * @return int
     */
    public function interval()
    {
        return 1000 * 60 * 5;
    }

    public function run(): void
    {
        ContractStatusJob::dispatch();
    }
}
