<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceClockRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 考勤打卡Dao
 * Class AttendanceClockDao.
 */
class AttendanceClockDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 搜索.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function search($where, ?bool $authWhere = null): mixed
    {
        $scope = $where['scope'] ?? '';
        if (isset($where['scope'])) {
            unset($where['scope']);
        }
        return parent::search($where, $authWhere)->when($scope, function ($query) use ($scope) {
            $query->{$scope == 1 ? 'whereIn' : 'whereNotIn'}('uid', function ($query) {
                $query->from('admin_info')->where('admin_info.type', 4)->select(['admin_info.id']);
            });
        });
    }

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return AttendanceClockRecord::class;
    }
}
