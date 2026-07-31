<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkGroupChatMember;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class WorkGroupChatMemberDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return WorkGroupChatMember::class;
    }
}
