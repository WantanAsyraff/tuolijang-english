<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 定时获取群发任务结果.
 */
class WorkMessagingResultCronJob extends CronJob
{
    public function interval()
    {
        return 45 * 1000;
    }

    public function run()
    {
        if (!sys_config('wechat_work_corpid')){
            return true;
        }
        WorkMessagingResultJob::dispatch();
    }
}
