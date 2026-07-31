<?php

declare(strict_types=1);


namespace App\Jobs\WorkExternalContact;

use App\Http\Service\WorkExternalContact\WorkMassMessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 创建群发任务
 */
class WorkMessagingAddJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected array|int $massId = []) {}

    public function handle(WorkMassMessagingService $messagingService)
    {
        if (! $this->massId) {
            $this->massId = $messagingService->column(['status' => 1, 'send_minute' => now()], 'id');
        }
        foreach (is_array($this->massId) ? $this->massId : [$this->massId] as $massId) {
            $messagingService->sendWorkMsg((int) $massId);
        }
    }
}
