<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Bill;

use App\Http\Service\Customer\PaymentService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class BillDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取账目详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '账目ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('账目ID');
        }

        return app(PaymentService::class)->getInfo(['id' => $id], ['*'], ['renew', 'card', 'treaty', 'client', 'attachs', 'invoice']);
    }
}
