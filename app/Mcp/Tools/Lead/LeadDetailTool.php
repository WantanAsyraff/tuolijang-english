<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Lead;

use App\Constants\System\ViewSearchEnum;
use App\Http\Service\Customer\LeadService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class LeadDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取线索详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '线索ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('线索ID');
        }

        return app(LeadService::class)->detail($id, $this->getUserDbId(), ViewSearchEnum::VIEW_CLUE);
    }
}
