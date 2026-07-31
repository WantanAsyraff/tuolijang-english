<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\RosterCycleShift;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 排班周期班次Dao
 * Class RosterCycleShiftDao.
 */
class RosterCycleShiftDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return RosterCycleShift::class;
    }
}
