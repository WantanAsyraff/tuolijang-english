<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 定时获取朋友圈结果.
 */
class WorkMomentResultCronJob extends CronJob
{
    public function interval()
    {
        return 30 * 1000;
    }

    public function run()
    {
        if (!sys_config('wechat_work_corpid')){
            return true;
        }
        WorkMomentResultJob::dispatch();
    }
}
