<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\CalendarConfig;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 日历设置Dao
 * Class CalendarConfigDao.
 */
class CalendarConfigDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return CalendarConfig::class;
    }
}
