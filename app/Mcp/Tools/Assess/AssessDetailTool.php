<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Assess;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Assess\AssessService;
use App\Mcp\Tools\Traits\InteractsWithService;

class AssessDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取绩效详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '绩效ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        if (empty($arguments['id'])) {
            return $this->missingId('绩效ID');
        }

        $service = app(AssessService::class);
        return $service->getAssessInfo((int) $arguments['id']);
    }
}
