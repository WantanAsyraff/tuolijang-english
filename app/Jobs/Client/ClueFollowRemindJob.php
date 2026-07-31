<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Constants\System\ConfigEnum;
use App\Http\Service\Customer\LeadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 线索未跟进提醒.
 */
class ClueFollowRemindJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public $uniqueFor = 300;

    public function __construct() {}

    public function handle(): void
    {
        try {
            $switch = (int) sys_config(ConfigEnum::CLUE_FOLLOW_SWITCH['key']);
            if ($switch < 1) {
                return;
            }
            app(LeadService::class)->followRemindTimer();
        } catch (\Throwable $e) {
            Log::error('线索跟进提醒失败:' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }
}
