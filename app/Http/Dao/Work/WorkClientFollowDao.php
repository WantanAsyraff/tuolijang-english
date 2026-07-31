<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkClientFollow;
use crmeb\basic\BaseDao;

/**
 * 客户跟进表.
 */
class WorkClientFollowDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel()
    {
        return WorkClientFollow::class;
    }
}
