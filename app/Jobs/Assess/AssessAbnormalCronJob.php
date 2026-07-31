<?php

declare(strict_types=1);


namespace App\Jobs\Assess;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 定时检测考核结束期没有创建绩效的人员提醒
 * Class AssessAbnormalCronJob.
 */
class AssessAbnormalCronJob extends CronJob
{
    /**
     * 频率：每10s运行一次
     * @return int
     */
    public function interval()
    {
        return 30000;
    }

    public function run()
    {
        AssessAbnormalJob::dispatchPeriods();
    }
}
