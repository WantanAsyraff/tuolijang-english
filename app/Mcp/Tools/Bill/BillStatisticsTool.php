<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Bill;

use App\Http\Service\Customer\PaymentService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class BillStatisticsTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取账目统计';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'customer_id' => ['type' => 'integer', 'description' => '客户ID'],
                'contract_id' => ['type' => 'integer', 'description' => '合同ID'],
                'start_date' => ['type' => 'string', 'description' => '开始日期'],
                'end_date' => ['type' => 'string', 'description' => '结束日期'],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $service = app(PaymentService::class);
        if (! empty($arguments['contract_id'])) {
            return $service->getContractStatistics((int) $arguments['contract_id'], 1);
        }
        if (! empty($arguments['customer_id'])) {
            return $service->getCustomerStatistics((int) $arguments['customer_id'], 1);
        }

        $where = ['entid' => 1];
        if ($date = $this->dateRange($arguments)) {
            $where['date'] = $date;
        }

        return [
            'total'          => $service->count($where),
            'approved'       => $service->count($where + ['status' => 1]),
            'pending'        => $service->count($where + ['status' => 0]),
            'income'         => $service->sum($where + ['types' => [0, 1], 'status' => 1], 'num'),
            'expend'         => $service->sum($where + ['types' => 2, 'status' => 1], 'num'),
            'review_income'  => $service->sum($where + ['types' => [0, 1], 'status' => 0], 'num'),
            'review_expend'  => $service->sum($where + ['types' => 2, 'status' => 0], 'num'),
        ];
    }
}
