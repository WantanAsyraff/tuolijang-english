<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkClient;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;

class WorkClientDao extends BaseDao
{
    use ListSearchTrait;
    use BatchSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return WorkClient::class;
    }
}
