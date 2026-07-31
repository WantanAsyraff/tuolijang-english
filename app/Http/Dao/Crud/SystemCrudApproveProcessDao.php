<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudApproveProcess;
use crmeb\basic\BaseDao;

class SystemCrudApproveProcessDao extends BaseDao
{
    protected function setModel()
    {
        return SystemCrudApproveProcess::class;
    }
}
