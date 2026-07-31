<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use App\Http\Service\WorkExternalContact\WorkMediaService;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

/**
 * 素材删除定时任务
 */
class WorkMediaDeleteCronJob extends CronJob
{
    /**
     * 频率：每5m运行一次
     * @return int
     */
    public function interval()
    {
        return 300000;
    }

    public function run()
    {
        if (!sys_config('wechat_work_corpid')){
            return true;
        }
        app()->get(WorkMediaService::class)->deleteUnlinkMedias();
    }
}
