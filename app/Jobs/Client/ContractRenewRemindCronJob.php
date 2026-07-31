<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Task\customer\ContractRenewRemindTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 订单急需续费提醒
 * 订单续费今日到期提醒
 * 订单续费过期提醒
 * Class ContractRenewRemindCronJob.
 */
class ContractRenewRemindCronJob extends CronJob
{
    /**
     * 频率：每30s运行一次
     * @return int
     */
    public function interval()
    {
        return 30000;
    }

    public function run()
    {
        Task::deliver(new ContractRenewRemindTask());
    }
}
