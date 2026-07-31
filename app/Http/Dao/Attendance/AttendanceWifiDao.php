<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceWifi;
use crmeb\basic\BaseDao;

/**
 * 考勤组WiFi
 * Class AttendanceWifiDao.
 */
class AttendanceWifiDao extends BaseDao
{
    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return AttendanceWifi::class;
    }
}
