<?php

declare(strict_types=1);


namespace App\Http\Dao\Other;

use App\Http\Model\Other\Task;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 任务表
 * Class TaskDao.
 */
class TaskDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return Task::class;
    }
}
