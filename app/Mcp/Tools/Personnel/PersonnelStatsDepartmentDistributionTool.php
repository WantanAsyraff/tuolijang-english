<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Http\Service\Admin\AdminService;
use App\Http\Service\Frame\FrameService;
use App\Mcp\Tools\Abstract\BaseTool;
use App\Mcp\Tools\Traits\ResolvesPersonnelEmploymentStatus;

/**
 * 部门人员分布统计工具
 * Class PersonnelStatsDepartmentDistributionTool.
 */
class PersonnelStatsDepartmentDistributionTool extends BaseTool
{
    use ResolvesPersonnelEmploymentStatus;

    public function getDescription(): string
    {
        return '获取部门人员分布统计';
    }

    public function getInputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'employment_status' => $this->personnelEmploymentStatusSchema('all'),
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $frameService     = app(FrameService::class);
        $adminService     = app(AdminService::class);
        $employmentStatus = $this->resolvePersonnelEmploymentStatus($arguments, 'all');
        $personnelType    = $this->personnelTypesForEmploymentStatus($employmentStatus);

        $frames      = $frameService->getList(['is_show' => 1], ['id', 'name'], ['sort' => 'asc']);
        $result      = [];
        $allowedUids = $this->isAdmin() ? [] : $this->getPersonnelDataUidsForEmploymentStatus($employmentStatus);

        foreach ($frames as $frame) {
            $frameId = (int) ($frame['id'] ?? $frame['value'] ?? 0);
            if (! $frameId) {
                continue;
            }

            $where = ['entid' => 1, 'status' => 1, 'frame_id' => $frameId, 'types' => $personnelType, 'time' => ''];
            if (! $this->isAdmin()) {
                $where['ids'] = $allowedUids ?: [-1];
            }
            $count    = $adminService->adminList($where, 1, 1)['count'] ?? 0;
            $result[] = [
                'frame_id'          => $frameId,
                'frame_name'        => $frame['name'] ?? $frame['label'] ?? '',
                'employment_status' => $employmentStatus,
                'count'             => $count,
            ];
        }

        return $result;
    }
}
