<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Work\WorkClientService;
use App\Http\Service\Work\WorkDepartmentService;
use App\Http\Service\Work\WorkMemberService;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;

class WorkCronJob extends CronJob
{
    /**
     * 频率：每120秒运行一次
     * @return int
     */
    public function interval()
    {
        return 120000;
    }

    public function run(): void
    {
        if (sys_config('wechat_work_user_switch')) {
            // 同步部门
            app()->get(WorkDepartmentService::class)->authDepartment();

            app()->get(WorkMemberService::class)->authUpdataMemberV1();

            if (sys_config('wechat_work_client_switch')) {
                // 同步客户
                //                app()->get(WorkClientService::class)->authGetExternalcontact();
            }
        }
    }
}
