<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\RosterCycle;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 排班周期Dao
 * Class RosterCycleDao.
 */
class RosterCycleDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return RosterCycle::class;
    }
}
