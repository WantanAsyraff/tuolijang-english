<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Task\customer\CustomerReturnRemindTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 客户退回公海提醒
 * Class CustomerReturnRemindCronJob.
 */
class CustomerReturnRemindCronJob extends CronJob
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        Task::deliver(new CustomerReturnRemindTask());
    }
}
