<?php

declare(strict_types=1);


namespace App\Http\Dao\Schedule;

use App\Http\Model\Schedule\ScheduleTask;
use crmeb\basic\BaseDao;

/**
 * 日程表.
 */
class ScheduleTaskDao extends BaseDao
{
    protected function setModel()
    {
        return ScheduleTask::class;
    }
}
