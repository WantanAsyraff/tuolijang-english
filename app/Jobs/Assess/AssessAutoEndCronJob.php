<?php

declare(strict_types=1);


namespace App\Jobs\Assess;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 自动结束考核
 * Class AssessAutoEndCronJob.
 */
class AssessAutoEndCronJob extends CronJob
{
    /**
     * 频率：每1小时运行一次
     * @return int
     */
    public function interval()
    {
        return 1000 * 60 * 60;
    }

    public function run()
    {
        AssessAutoEndJob::dispatch();
    }
}
