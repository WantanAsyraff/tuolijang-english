<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Http\Service\Customer\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 客户退回公海事件
 * Class CustomerReturnTask.
 */
class CustomerReturnJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public $uniqueFor = 300;

    protected int $limit = 50;

    public function __construct() {}

    public function handle(): void
    {
        try {
            $switch = (int) sys_config('return_high_seas_switch');
            if ($switch < 1) {
                return;
            }

            app()->get(CustomerService::class)->autoReturnTimer();
        } catch (\Throwable $e) {
            Log::error('客户自动退回公海失败:' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
