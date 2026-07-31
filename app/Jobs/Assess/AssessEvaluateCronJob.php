<?php

declare(strict_types=1);


namespace App\Jobs\Assess;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 自我评价提醒和开启考核任务提醒
 * Class AssessEvaluateCronJob.
 */
class AssessEvaluateCronJob extends CronJob
{
    /**
     * 频率：每2秒运行一次
     * @return int
     */
    public function interval()
    {
        return 20000;
    }

    public function run()
    {
        AssessEvaluateJob::dispatch();
    }
}
