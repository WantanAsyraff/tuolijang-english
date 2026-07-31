<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkGroupChat;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class WorkGroupChatDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return WorkGroupChat::class;
    }
}
