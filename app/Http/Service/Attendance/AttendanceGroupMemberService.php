<?php

declare(strict_types=1);


namespace App\Http\Service\Attendance;

use App\Http\Dao\Attendance\AttendanceGroupMemberDao;
use crmeb\basic\BaseService;

/**
 * @mixin AttendanceGroupMemberDao
 */
class AttendanceGroupMemberService extends BaseService
{
    public function __construct(AttendanceGroupMemberDao $dao)
    {
        $this->dao = $dao;
    }
}
