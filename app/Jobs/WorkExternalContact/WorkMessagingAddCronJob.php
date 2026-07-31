<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use App\Http\Service\WorkExternalContact\WorkMassMessagingService;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

/**
 * 创建群发定时任务
 */
class WorkMessagingAddCronJob extends CronJob
{
    public function interval()
    {
        return 45000;
    }

    public function run()
    {
        try {
            if (!sys_config('wechat_work_corpid')){
                return true;
            }
            $messagingService = app(WorkMassMessagingService::class);
            $massIds          = $messagingService->column(['status' => 1, 'send_minute' => now()], 'id');
            foreach ($massIds as $massId) {
                $messagingService->sendWorkMsg((int) $massId);
            }
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
