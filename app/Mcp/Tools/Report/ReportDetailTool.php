<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Report;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Report\ReportService;
use App\Mcp\Tools\Traits\InteractsWithService;

class ReportDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取汇报详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '汇报ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        if (empty($arguments['id'])) {
            return $this->missingId('汇报ID');
        }

        $service = app(ReportService::class);
        return $service->resourceEdit((int) $arguments['id']);
    }
}
