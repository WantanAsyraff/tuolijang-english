<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Contract;

use App\Http\Service\Customer\ContractService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class ContractStatisticsTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取合同统计';
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
        $service = app(ContractService::class);
        $where   = [];

        if ($date = $this->dateRange($arguments)) {
            $where['sign_time'] = $date;
        }

        return [
            'total'       => $service->count($where),
            'draft'       => $service->count($where + ['status' => 0]),
            'signing'     => $service->count($where + ['status' => 1]),
            'wait_sign'   => $service->count($where + ['status' => 2]),
            'completed'   => $service->count($where + ['status' => 3]),
            'rejected'    => $service->count($where + ['status' => 4]),
            'revoked'     => $service->count($where + ['status' => 6]),
            'not_started' => $service->count($where + ['fail_status' => 1]),
            'expired'     => $service->count($where + ['status' => 5]),
        ];
    }
}
