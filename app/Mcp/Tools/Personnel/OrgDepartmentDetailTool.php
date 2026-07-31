<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Frame\FrameService;

/**
 * 部门详情工具
 * Class OrgDepartmentDetailTool.
 */
class OrgDepartmentDetailTool extends BaseTool
{
    public function getDescription(): string
    {
        return '获取部门详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'frame_id' => ['type' => 'integer', 'description' => '部门ID'],
            ],
            'required' => ['frame_id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $service = app(FrameService::class);
        return $service->info($arguments['frame_id'] ?? 0);
    }
}
