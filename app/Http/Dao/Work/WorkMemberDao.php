<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkMember;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;

class WorkMemberDao extends BaseDao
{
    use BatchSearchTrait;
    use ListSearchTrait;

    protected function setModel()
    {
        return WorkMember::class;
    }
}
