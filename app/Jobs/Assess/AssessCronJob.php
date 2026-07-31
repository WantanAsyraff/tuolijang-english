<?php

declare(strict_types=1);


namespace App\Jobs\Assess;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 绩效创建提醒
 * Class AssessCronJob.
 */
class AssessCronJob extends CronJob
{
    /**
     * 频率：每2m运行一次
     * @return int
     */
    public function interval()
    {
        return 1000 * 60 * 2;
    }

    public function run()
    {
        AssessJob::dispatch();
    }
}
