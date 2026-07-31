<?php

declare(strict_types=1);


namespace App\Listeners\swoole;

use App\Http\Service\System\RolesService;
use Hhxsv5\LaravelS\Swoole\Events\WorkerStartInterface;
use Illuminate\Support\Facades\Cache;
use Swoole\Http\Server;

/**
 * swoole启动事件
 * Class SwooleStart.
 */
class SwooleStart implements WorkerStartInterface
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    public function handle(Server $server, $workerId)
    {
        if (! $workerId && file_exists(public_path('install/install.lock'))) {
            $lockKey = 'swoole:init-rules:lock';
            if (! Cache::add($lockKey, getmypid(), 60)) {
                return;
            }

            try {
                app()->get(RolesService::class)->initRules();
            } finally {
                Cache::forget($lockKey);
            }
        }
    }
}
