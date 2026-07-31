<?php

declare(strict_types=1);


namespace App\Http\Dao\Schedule;

use App\Http\Model\Schedule\ScheduleRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 日程提醒记录表.
 */
class ScheduleRecordDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return ScheduleRecord::class;
    }
}
