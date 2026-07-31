<?php

declare(strict_types=1);


namespace App\Http\Dao\Attendance;

use App\Http\Model\Attendance\AttendanceHandleRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 处理记录Dao
 * Class AttendanceHandleRecordDao.
 */
class AttendanceHandleRecordDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return AttendanceHandleRecord::class;
    }
}
