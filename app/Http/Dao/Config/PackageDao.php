<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Models\system\admin\SystemPackage;
use crmeb\basic\BaseDao;

class PackageDao extends BaseDao
{
    public function setModel()
    {
        return SystemPackage::class;
    }
}
