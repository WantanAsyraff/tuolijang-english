<?php

namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkMemberRelation;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

/**
 * 成员关系
 * Class WorkMemberRelationDao
 */
class WorkMemberRelationDao extends BaseDao
{
    use BatchSearchTrait;


    protected function setModel()
    {
        return WorkMemberRelation::class;
    }
}
