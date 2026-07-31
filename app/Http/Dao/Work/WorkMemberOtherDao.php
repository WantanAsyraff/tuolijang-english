<?php

namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkMemberOther;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

/**
 * 其他成员信息
 * Class WorkMemberOtherDao
 */
class WorkMemberOtherDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return WorkMemberOther::class;
    }
}
