<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Contract;

use App\Http\Service\Customer\ContractService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class ContractDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取合同详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '合同ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('合同ID');
        }

        $info = app(ContractService::class)->get($id, ['*'], ['admin', 'signatory', 'customer', 'attach', 'result']);
        if (! $info) {
            return ['error' => true, 'message' => '合同不存在'];
        }
        return $this->toArray($info);
    }
}
