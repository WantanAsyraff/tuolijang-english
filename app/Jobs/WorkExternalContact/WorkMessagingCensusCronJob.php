<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use App\Http\Service\WorkExternalContact\WorkMassMessagingResultService;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

/**
 * 统计群发消息结果.
 */
class WorkMessagingCensusCronJob extends CronJob
{
    public function interval()
    {
        return 60000;
    }

    public function run()
    {
        try {
            app(WorkMassMessagingResultService::class)->censusResult();
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
