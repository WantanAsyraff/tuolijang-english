<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Finance;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Finance\BillService;
use App\Mcp\Tools\Traits\InteractsWithService;

class FinanceTrendTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取资金趋势分析';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'time' => ['type' => 'string', 'description' => '时间范围'],
                'start_date' => ['type' => 'string', 'description' => '开始日期，YYYY-MM-DD'],
                'end_date' => ['type' => 'string', 'description' => '结束日期，YYYY-MM-DD'],
                'income' => ['type' => 'integer', 'description' => '收入'],
                'expend' => ['type' => 'integer', 'description' => '支出'],
                'all' => ['type' => 'boolean', 'description' => '是否全部'],
                'cate_id' => ['type' => 'array', 'description' => '分类ID列表'],
                'cate_ids' => ['type' => 'array', 'description' => '分类ID列表别名'],
                'type' => ['type' => 'string', 'description' => '收支过滤：1=仅收入，0=仅支出，空=全部'],
                'user_id' => ['type' => 'array', 'description' => '创建人用户ID列表，将自动按数据权限取交集', 'items' => ['type' => 'integer']],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $service = app(BillService::class);
        $requestedUserIds = (array) ($arguments['user_id'] ?? []);
        $allowedUserIds   = $this->isAdmin() ? [] : $this->getDataUids('bill_list', 1);
        $userIds          = $requestedUserIds
            ? ($allowedUserIds ? array_values(array_intersect(array_map('intval', $requestedUserIds), $allowedUserIds)) : array_map('intval', $requestedUserIds))
            : ($this->isAdmin() ? [] : ($allowedUserIds ?: [-1]));
        $cateIds = (array) ($arguments['cate_id'] ?? $arguments['cate_ids'] ?? []);
        if ($cateIds && is_array(reset($cateIds))) {
            $cateIds = array_values(array_unique(array_merge(...$cateIds)));
        }

        return $service->getTrend(
            $this->timeRange($arguments),
            $arguments['income'] ?? true,
            $arguments['expend'] ?? true,
            $arguments['all'] ?? true,
            $cateIds,
            (string) ($arguments['type'] ?? ''),
            $userIds
        );
    }
}
