<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceShiftRule;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 考勤班次规则Dao
 * Class AttendanceShiftRuleDao.
 */
class AttendanceShiftRuleDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return AttendanceShiftRule::class;
    }
}
