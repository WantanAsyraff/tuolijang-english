<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Customer\LabelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 同步企业微信标签到平台.
 */
class WorkWorkToPlatformLabelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected ?string $traceId = null) {}

    public function handle()
    {
        $traceId = $this->traceId ?: uniqid('work_label_sync_', true);
        Log::warning('企业微信标签同步到平台队列开始', [
            'trace_id' => $traceId,
        ]);
        app()->get(LabelService::class)->authWorkLabel([], [], $traceId);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('企业微信标签同步到平台队列失败', [
            'trace_id' => $this->traceId ?? '',
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'message'   => $e->getMessage(),
        ]);
    }
}
