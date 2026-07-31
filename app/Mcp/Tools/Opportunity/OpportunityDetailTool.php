<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Opportunity;

use App\Constants\System\ViewSearchEnum;
use App\Http\Service\Customer\OpportunityService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class OpportunityDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取商机详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '商机ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('商机ID');
        }

        return app(OpportunityService::class)->detail($id, $this->getUserDbId(), ViewSearchEnum::VIEW_ODDS);
    }
}
