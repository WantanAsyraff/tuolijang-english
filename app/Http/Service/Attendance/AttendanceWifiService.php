<?php

declare(strict_types=1);


namespace App\Http\Service\Attendance;

use App\Http\Dao\Attendance\AttendanceWifiDao;
use crmeb\basic\BaseService;

/**
 * 考勤组Wifi记录
 * Class AttendanceWifiService.
 */
class AttendanceWifiService extends BaseService
{
    public function __construct(AttendanceWifiDao $dao)
    {
        $this->dao = $dao;
    }
}
