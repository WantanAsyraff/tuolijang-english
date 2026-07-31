<?php

declare(strict_types=1);


namespace App\Http\Dao\Schedule;

use App\Http\Model\Schedule\ScheduleUser;
use crmeb\basic\BaseDao;

/**
 * 日程表.
 */
class ScheduleUserDao extends BaseDao
{
    protected function setModel()
    {
        return ScheduleUser::class;
    }
}
