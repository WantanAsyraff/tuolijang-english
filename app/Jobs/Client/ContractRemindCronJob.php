<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 订单定时任务
 * 发送：订单即将到期提醒、订单今日到期提醒
 * Class ContractCronJob.
 */
class ContractRemindCronJob extends CronJob
{
    /**
     * 频率：每30s小时运行一次
     * @return int
     */
    public function interval()
    {
        return 30000;
    }

    public function run(): void
    {
        ContractRemindJob::dispatch();
    }
}
