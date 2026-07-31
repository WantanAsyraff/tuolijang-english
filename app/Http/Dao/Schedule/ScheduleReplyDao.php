<?php

declare(strict_types=1);


namespace App\Http\Dao\Schedule;

use App\Http\Model\Schedule\ScheduleReply;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 日程表.
 */
class ScheduleReplyDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return ScheduleReply::class;
    }
}
