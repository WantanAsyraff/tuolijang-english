<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Record;

use App\Constants\ModuleEnum;
use App\Constants\CustomEnum\CustomEnum;
use App\Http\Service\Customer\RecordService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;
use App\Mcp\Tools\Traits\ResolvesTargetUserArguments;

class RecordListTool extends BaseTool
{
    use InteractsWithService;
    use ResolvesTargetUserArguments;

    public function getDescription(): string
    {
        return '获取客户跟进记录列表';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => array_merge([
                'customer_id' => ['type' => 'integer', 'description' => '客户ID'],
                'types' => ['type' => 'integer', 'description' => '跟进类型'],
                'link_type' => ['type' => 'string', 'description' => '关联类型，默认 customer'],
                'keyword' => ['type' => 'string', 'description' => '搜索关键词'],
                'start_date' => ['type' => 'string', 'description' => '跟进开始日期'],
                'end_date' => ['type' => 'string', 'description' => '跟进结束日期'],
                'page' => ['type' => 'integer', 'description' => '页码', 'default' => 1],
                'limit' => ['type' => 'integer', 'description' => '每页数量', 'default' => 20],
            ], $this->targetUserSchemaProperties()),
        ];
    }

    public function execute(array $arguments): array
    {
        $this->applyPage($arguments);

        $where = $this->onlyFilled($arguments, [
            'customer_id' => 'eid',
            'types' => 'type',
            'keyword' => 'content',
        ]);
        $where['link_type'] = $arguments['link_type'] ?? CustomEnum::CUSTOMER;

        if ($date = $this->dateRange($arguments)) {
            $where['time'] = $date;
        }

        $authorized = $this->resolveAuthorizedUserIds($arguments, ModuleEnum::CUSTOMER);
        if (! ($authorized['success'] ?? false)) {
            return $authorized['error'];
        }
        if (! empty($authorized['has_filter'])) {
            $where['creator_uid'] = $authorized['user_ids'];
        }

        return app(RecordService::class)->getList($where);
    }
}
