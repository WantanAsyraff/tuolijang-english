<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Frame\FrameService;

/**
 * 部门列表工具
 * Class OrgDepartmentListTool.
 */
class OrgDepartmentListTool extends BaseTool
{
    public function getDescription(): string
    {
        return '获取部门列表（基于数据权限过滤）';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass(),
        ];
    }

    public function execute(array $arguments): array
    {
        $service = app(FrameService::class);
        return $service->getList(['status' => 1], ['*'], ['sort' => 'asc']);
    }
}
