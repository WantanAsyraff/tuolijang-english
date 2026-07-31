<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudApproveRule;
use crmeb\basic\BaseDao;

class SystemCrudApproveRuleDao extends BaseDao
{
    protected function setModel()
    {
        return SystemCrudApproveRule::class;
    }
}
