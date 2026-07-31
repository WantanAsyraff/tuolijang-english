<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Order;

use App\Constants\CustomEnum\ContractEnum;
use App\Http\Service\Customer\OrderService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class OrderStatisticsTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取订单统计';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'start_date' => ['type' => 'string', 'description' => '开始日期'],
                'end_date' => ['type' => 'string', 'description' => '结束日期'],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $service = app(OrderService::class);
        $where   = [];
        if ($date = $this->dateRange($arguments)) {
            $where['created_at'] = $date;
        }

        $uuid = (string) ($this->getUserInfo()['uid'] ?? '');
        $listStatistics = $uuid ? $service->getListStatistics(ContractEnum::CONTRACT_VIEWER, $uuid) : [];

        return array_merge($listStatistics, [
            'filtered_total'  => $service->count($where),
            'filtered_amount' => $service->sum($where, 'contract_price'),
            'signed_amount'   => $service->sum($where + ['signing_status' => 1], 'contract_price'),
            'unpaid_amount'   => $service->sum($where, 'surplus'),
        ]);
    }
}
