<?php

declare(strict_types=1);


namespace App\Http\Dao\Cloud;

use App\Http\Model\Cloud\CloudShare;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class CloudShareDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return CloudShare::class;
    }
}
