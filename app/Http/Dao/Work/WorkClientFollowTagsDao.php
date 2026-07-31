<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkClientFollowTags;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class WorkClientFollowTagsDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return WorkClientFollowTags::class;
    }
}
