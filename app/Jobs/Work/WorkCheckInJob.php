<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Attendance\AttendanceClockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * 同步考勤数据.
 */
class WorkCheckInJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public $uniqueFor = 300;

    public function __construct(protected Carbon $startTime, protected Carbon $endTime) {}

    public function uniqueId(): string
    {
        return $this->startTime->format('Y-m-d H:i') . ':' . $this->endTime->format('Y-m-d H:i');
    }

    public function handle()
    {
        try {
            if (sys_config('wechat_work_corpid') && sys_config('wechat_work_token')){
                app()->get(AttendanceClockService::class)->syncWorkClockRecord($this->startTime, $this->endTime);
            }
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
