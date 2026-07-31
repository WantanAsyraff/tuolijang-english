<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Record;

use App\Http\Service\Customer\RecordService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\InteractsWithService;

class RecordDetailTool extends BaseTool
{
    use InteractsWithService;

    public function getDescription(): string
    {
        return '获取跟进记录详情';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => '跟进记录ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $id = (int) ($arguments['id'] ?? 0);
        if (! $id) {
            return $this->missingId('跟进记录ID');
        }

        $info = app(RecordService::class)->get($id, ['*'], ['creator', 'follow']);
        if (! $info) {
            return ['error' => true, 'message' => '跟进记录不存在'];
        }
        return $this->toArray($info);
    }
}
