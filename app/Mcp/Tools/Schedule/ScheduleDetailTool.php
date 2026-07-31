<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Schedule;

use App\Http\Service\Schedule\ScheduleService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class ScheduleDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取日程详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '日程ID'],
                'start_time' => ['type' => 'string', 'description' => '周期日程实例开始时间'],
                'end_time' => ['type' => 'string', 'description' => '周期日程实例结束时间'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('日程ID');
        }

        $service = app(ScheduleService::class);
        if (! empty($arguments['start_time']) && ! empty($arguments['end_time'])) {
            $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'days', 'rate', 'remind as is_remind', 'link_id', 'fail_time'];
            return $service->scheduleInfo($id, $this->getUserDbId(), $field, [
                'start_time' => $arguments['start_time'],
                'end_time'   => $arguments['end_time'],
            ]);
        }

        $info = $service->get($id, ['*'], ['master', 'user', 'type', 'remind']);
        if (! $info) {
            return ['error' => true, 'message' => '日程不存在'];
        }
        return $this->toArray($info);
    }
}
