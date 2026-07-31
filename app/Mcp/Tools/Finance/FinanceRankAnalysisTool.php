<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Finance;

use App\Mcp\Tools\Abstract\BaseTool;
use App\Http\Service\Finance\BillService;
use App\Mcp\Tools\Traits\InteractsWithService;

class FinanceRankAnalysisTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取资金占比分析';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'time' => ['type' => 'string', 'description' => '时间范围'],
                'start_date' => ['type' => 'string', 'description' => '开始日期，YYYY-MM-DD'],
                'end_date' => ['type' => 'string', 'description' => '结束日期，YYYY-MM-DD'],
                'cate_id' => ['type' => 'integer', 'description' => '分类ID'],
                'types' => ['type' => 'integer', 'description' => '类型'],
                'cate_ids' => ['type' => 'array', 'description' => '分类ID列表'],
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
        $cateIds = (array) ($arguments['cate_ids'] ?? []);
        if ($cateIds && is_array(reset($cateIds))) {
            $cateIds = array_values(array_unique(array_merge(...$cateIds)));
        }

        return $service->getRankAnalysis(
            $this->timeRange($arguments),
            (int) ($arguments['cate_id'] ?? 0),
            (int) ($arguments['types'] ?? 0),
            $cateIds,
            $userIds
        );
    }
}
