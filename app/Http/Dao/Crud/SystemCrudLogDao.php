<?php

namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudLog;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 日志
 */
class SystemCrudLogDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return SystemCrudLog::class;
    }
}
