<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Attendance;

use App\Constants\ModuleEnum;
use App\Http\Service\Attendance\AttendancePersonnelStatisticsService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;
use App\Mcp\Tools\Traits\ResolvesTargetUserArguments;

class AttendanceSummaryTool extends BaseTool
{
    use InteractsWithService;
    use ResolvesTargetUserArguments;

    public function getDescription(): string
    {
        return '获取考勤汇总报表';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => array_merge([
                'frame_id' => ['type' => 'integer', 'description' => '部门ID'],
                'group_id' => ['type' => 'integer', 'description' => '考勤组ID'],
                'user_id' => ['type' => 'array', 'description' => '用户ID列表', 'items' => ['type' => 'integer']],
                'scope' => ['type' => 'integer', 'description' => '人员范围：1=非正式员工，其他=正式员工'],
                'status' => ['type' => 'integer', 'description' => '考勤状态'],
                'month' => ['type' => 'string', 'description' => '月份，YYYY-MM'],
            ], $this->targetUserSchemaProperties()),
        ];
    }

    public function execute(array $arguments): array
    {
        $this->applyPage($arguments);
        $authorized = $this->resolveAuthorizedUserIds($arguments, ModuleEnum::ATTENDANCE);
        if (! ($authorized['success'] ?? false)) {
            return $authorized['error'];
        }
        if (! empty($authorized['has_filter'])) {
            $arguments['user_id'] = $authorized['user_ids'];
        }

        $where = $this->onlyFilled($arguments, [
            'frame_id',
            'group_id',
        ]);
        if (! empty($arguments['user_id'])) {
            $where['user_id'] = $this->intArray($arguments['user_id']);
        }
        $where['month'] = $arguments['month'] ?? '';
        $where['scope'] = $arguments['scope'] ?? '';
        $where['personnel_status'] = $arguments['status'] ?? '';

        return app(AttendancePersonnelStatisticsService::class)->getMonthlyStatistics($this->getUserDbId(), $where);
    }
}
