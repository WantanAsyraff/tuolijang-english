<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Invoice;

use App\Http\Service\Customer\InvoiceService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class InvoiceDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取发票详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '发票ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('发票ID');
        }

        return app(InvoiceService::class)->getInfo(['id' => $id], ['*'], ['card', 'treaty', 'client', 'attachs', 'category']);
    }
}
