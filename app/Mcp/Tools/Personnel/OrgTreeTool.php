<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Frame\FrameService;

/**
 * 组织架构树工具
 * Class OrgTreeTool.
 */
class OrgTreeTool extends BaseTool
{
    public function getDescription(): string
    {
        return '获取组织架构树（基于数据权限，只返回有权限的部门）';
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
        $tree = $service->tree();
        if ($this->isAdmin()) {
            return $tree;
        }

        [$frameIds] = $this->getMcpRequest()->getDataFrames(1);
        if (! $frameIds) {
            return [];
        }

        return $this->filterTree((array) $tree, array_map('intval', $frameIds));
    }

    protected function filterTree(array $nodes, array $frameIds): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $children = $this->filterTree((array) ($node['children'] ?? []), $frameIds);
            if (in_array((int) ($node['id'] ?? 0), $frameIds, true) || $children) {
                $node['children'] = $children;
                $result[] = $node;
            }
        }
        return $result;
    }
}
