<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Attendance;

use App\Http\Service\Attendance\AttendanceApplyRecordService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class AttendanceApplyDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取请假/补卡申请详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '申请记录ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('申请记录ID');
        }

        $info = app(AttendanceApplyRecordService::class)->get($id);
        if (! $info) {
            return ['error' => true, 'message' => '考勤申请记录不存在'];
        }
        return $this->toArray($info);
    }
}
