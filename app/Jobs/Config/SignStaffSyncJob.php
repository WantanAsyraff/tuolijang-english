<?php

declare(strict_types=1);


namespace App\Jobs\Config;

use App\Http\Service\Admin\AdminService;
use crmeb\services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SignStaffSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected int $page = 1) {}

    public function handle()
    {
        try {
            if (sys_config('e_signature')) {
                $smsService   = app(SmsService::class);
                $adminService = app(AdminService::class);
                do {
                    $list = $smsService->getSignOperatorList($this->page);
                    foreach ($list as $item) {
                        $adminService->update(['phone' => $item['mobile']], ['e_sign' => 1, 'e_userid' => $item['userid'], 'e_openid' => $item['openid']]);
                    }
                    ++$this->page;
                } while (count($list) >= 10);
            }
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
