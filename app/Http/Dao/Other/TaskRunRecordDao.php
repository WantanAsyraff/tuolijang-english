<?php

declare(strict_types=1);


namespace App\Http\Dao\Other;

use App\Http\Model\Other\TaskRunRecord;
use crmeb\basic\BaseDao;

/**
 * 任务运行记录
 * Class TaskRunRecordDao.
 */
class TaskRunRecordDao extends BaseDao
{
    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return TaskRunRecord::class;
    }
}
