<?php

declare(strict_types=1);


namespace App\Task\frame;

use App\Http\Service\Frame\FrameService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 统计企业人数
 * Class FrameCensusTask.
 */
class FrameCensusTask extends Task
{
    public function __construct() {}

    public function handle()
    {
        try {
            app()->get(FrameService::class)->handleStatistics();
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
