<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 客户自动退回公海任务
 * Class CustomerReturnCronJob.
 */
class CustomerReturnCronJob extends CronJob
{
    /**
     * 频率：每2小时运行一次
     * @return int
     */
    public function interval()
    {
        return 1000 * 60 * 60 * 2;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        CustomerReturnJob::dispatch();
    }
}
