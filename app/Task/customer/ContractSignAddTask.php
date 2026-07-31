<?php

declare(strict_types=1);


namespace App\Task\customer;

use App\Http\Service\Customer\ContractService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 发起电子签约流程.
 */
class ContractSignAddTask extends Task
{
    public function __construct(protected int $docId) {}

    public function handle()
    {
        try {
            app(ContractService::class)->addSignProcess($this->docId);
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
