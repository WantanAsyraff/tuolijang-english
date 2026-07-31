<?php

declare(strict_types=1);


namespace App\Http\Service\Attendance;

use App\Http\Dao\Attendance\AttendanceWhitelistDao;
use crmeb\basic\BaseService;

class AttendanceWhitelistService extends BaseService
{
    public function __construct(AttendanceWhitelistDao $dao)
    {
        $this->dao = $dao;
    }
}
