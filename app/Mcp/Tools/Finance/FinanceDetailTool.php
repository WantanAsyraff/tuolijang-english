<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Finance;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Finance\BillService;
use App\Mcp\Tools\Traits\InteractsWithService;

class FinanceDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取财务流水详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '流水ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('流水ID');
        }

        $service = app(BillService::class);
        return $service->detail($id, $this->isAdmin() ? [] : ($this->getDataUids('bill_list', 1) ?: [-1]));
    }
}
