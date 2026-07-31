<?php

namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudOperate;
use crmeb\basic\BaseDao;

class SystemCrudOperateDao extends BaseDao
{
    protected function setModel()
    {
        return SystemCrudOperate::class;
    }
}
