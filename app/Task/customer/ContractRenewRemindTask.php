<?php

declare(strict_types=1);


namespace App\Task\customer;

use App\Http\Service\Customer\PaymentService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 订单续费提醒事件
 * Class ContractRenewRemindTask.
 */
class ContractRenewRemindTask extends Task
{
    protected int $limit = 50;

    public function __construct() {}

    public function handle(): void
    {
        try {
            $clientService = app()->get(PaymentService::class);
            $entList       = $clientService->getEntCacheList();
            if (! $entList) {
                return;
            }
            foreach ($entList as $item) {
                $sumCount  = $clientService->clientBillCountCache((int) $item['entid']);
                $pageCount = ceil($sumCount / $this->limit);
                for ($i = 1; $i <= $pageCount; ++$i) {
                    $clientService->timer($item['entid'], $i, $this->limit);
                }
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
