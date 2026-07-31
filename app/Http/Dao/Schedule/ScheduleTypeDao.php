<?php

declare(strict_types=1);


namespace App\Http\Dao\Schedule;

use App\Http\Model\Schedule\ScheduleType;
use crmeb\basic\BaseDao;

/**
 * 日程类型表.
 */
class ScheduleTypeDao extends BaseDao
{
    protected function setModel()
    {
        return ScheduleType::class;
    }
}
