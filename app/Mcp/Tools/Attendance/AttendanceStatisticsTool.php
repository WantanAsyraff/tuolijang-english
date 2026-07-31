<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Attendance;

use App\Constants\ModuleEnum;
use App\Http\Service\Attendance\AttendancePersonnelStatisticsService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;
use App\Mcp\Tools\Traits\ResolvesTargetUserArguments;

class AttendanceStatisticsTool extends BaseTool
{
    use InteractsWithService;
    use ResolvesTargetUserArguments;

    public function getDescription(): string
    {
        return '获取考勤统计汇总';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => array_merge([
                'user_id' => ['type' => 'integer', 'description' => '用户ID'],
                'month' => ['type' => 'string', 'description' => '月份筛选'],
                'start_date' => ['type' => 'string', 'description' => '开始日期'],
                'end_date' => ['type' => 'string', 'description' => '结束日期'],
            ], $this->targetUserSchemaProperties()),
        ];
    }

    public function execute(array $arguments): array
    {
        $authorized = $this->resolveAuthorizedUserIds($arguments, ModuleEnum::ATTENDANCE);
        if (! ($authorized['success'] ?? false)) {
            return $authorized['error'];
        }
        $userId = ! empty($authorized['has_filter'])
            ? (int) ($authorized['user_ids'][0] ?? 0)
            : (int) ($arguments['user_id'] ?? $this->getUserDbId());
        $time = $arguments['month'] ?? '';
        if (! $time && ! empty($arguments['start_date']) && ! empty($arguments['end_date'])) {
            $time = str_replace('-', '/', $arguments['start_date']) . '-' . str_replace('-', '/', $arguments['end_date']);
        }
        $time = $time ?: 'month';

        return app(AttendancePersonnelStatisticsService::class)->getAttendanceStatistics(
            $this->getUserDbId(),
            $userId ?: $this->getUserDbId(),
            $time
        );
    }
}
