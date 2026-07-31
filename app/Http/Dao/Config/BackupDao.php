<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\Backup;
use crmeb\basic\BaseDao;

class BackupDao extends BaseDao
{
    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return Backup::class;
    }
}
