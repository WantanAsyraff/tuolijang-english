<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceArrangeRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 排班数据Dao
 * Class AttendanceArrangeRecordDao.
 */
class AttendanceArrangeRecordDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return AttendanceArrangeRecord::class;
    }
}
