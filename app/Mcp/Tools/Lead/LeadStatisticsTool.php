<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Lead;

use App\Http\Service\Customer\LeadService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class LeadStatisticsTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取线索统计';
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
        $service = app(LeadService::class);
        $where   = $this->onlyFilled($arguments, ['status']);
        if ($date = $this->dateRange($arguments)) {
            $where['time'] = $date;
        }

        return [
            'total'      => $service->count($where),
            'unassigned' => $service->count($where + ['uid' => 0]),
            'assigned'   => $service->count($where + ['not_uid' => 0]),
            'converted'  => $service->count($where + ['is_work' => 1]),
        ];
    }
}
