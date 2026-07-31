<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Order;

use App\Constants\System\ViewSearchEnum;
use App\Http\Service\Customer\OrderService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class OrderDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取订单详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '订单ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('订单ID');
        }

        return app(OrderService::class)->detail($id, $this->getUserDbId(), ViewSearchEnum::VIEW_CONTRACT);
    }
}
