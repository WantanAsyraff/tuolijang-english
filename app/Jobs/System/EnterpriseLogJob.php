<?php

declare(strict_types=1);


namespace App\Jobs\System;

use App\Http\Service\System\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnterpriseLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 30;

    public $failOnTimeout = true;

    public function __construct(private array $data) {}

    public function handle(): void
    {
        try {
            app(LogService::class)->createLogFromData($this->data);
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
