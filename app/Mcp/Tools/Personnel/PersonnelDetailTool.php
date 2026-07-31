<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Http\Service\Admin\AdminInfoService;
use App\Http\Service\Admin\AdminService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\FiltersPersonnelOutput;

/**
 * 人员详情工具
 * Class PersonnelDetailTool.
 */
class PersonnelDetailTool extends BaseTool
{
    use FiltersPersonnelOutput;

    public function getDescription(): string
    {
        return '获取人员详情（基于数据权限，自动脱敏敏感字段）';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => ['type' => 'integer', 'description' => '用户ID'],
            ],
            'required' => ['user_id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $userId = (int) ($arguments['user_id'] ?? 0);
        if (! $userId) {
            return ['error' => true, 'message' => '用户ID不能为空'];
        }
        if (! $this->hasPermissionToUser($userId, 'personnel')) {
            return ['error' => true, 'message' => '无权查看该人员信息'];
        }

        $service = app(AdminService::class);
        $info    = $service->getInfo(
            $userId,
            ['id', 'uid', 'account', 'name', 'avatar', 'phone', 'job', 'status', 'created_at', 'updated_at'],
            [
                'job'   => fn ($query) => $query->select($this->safePersonnelJobFields()),
                'frames',
                'manage_frames',
                'super' => fn ($query) => $query->select(['admin.id', 'uid', 'name', 'avatar']),
            ]
        );
        $cardInfo = app(AdminInfoService::class)->get(['id' => $userId], ['birthday', 'sex', 'type', 'work_time', 'quit_time'])?->toArray() ?: [];
        $info     = array_merge($info, $cardInfo);

        return $this->filterPersonnelItem($info);
    }
}
