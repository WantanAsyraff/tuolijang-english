<?php

declare(strict_types=1);


namespace App\Http\Dao\Cloud;

use App\Http\Model\Cloud\CloudAuth;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class CloudAuthDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return CloudAuth::class;
    }
}
