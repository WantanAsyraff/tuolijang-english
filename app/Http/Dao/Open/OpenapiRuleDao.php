<?php

namespace App\Http\Dao\Open;

use App\Http\Model\Open\OpenapiRule;
use crmeb\basic\BaseDao;

class OpenapiRuleDao extends BaseDao
{

    protected function setModel()
    {
        return OpenapiRule::class;
    }
}
