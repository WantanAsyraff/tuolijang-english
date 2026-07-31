<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Customer;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Customer\CustomerService;
use App\Mcp\Tools\Traits\InteractsWithService;

class GetCustomerTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '根据ID获取客户详细信息';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '客户ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = $arguments['id'] ?? 0;
        if (!$id) {
            return ['error' => true, 'message' => '客户ID不能为空'];
        }

        $service = app(CustomerService::class);
        $customer = $service->get(['id' => $id]);

        if (!$customer) {
            return ['error' => true, 'message' => '客户不存在'];
        }

        return $customer->toArray();
    }
}
