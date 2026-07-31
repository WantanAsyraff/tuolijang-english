<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceShortRemind;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 考勤缺卡提醒Dao
 * Class AttendanceShortRemindDao.
 */
class AttendanceShortRemindDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return AttendanceShortRemind::class;
    }
}
