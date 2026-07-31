<?php

declare(strict_types=1);


namespace App\Http\Service\System;

use App\Http\Dao\Config\BackupDao;
use crmeb\basic\BaseService;

class SystemBackupService extends BaseService
{
    /**
     * SystemBackupService constructor.
     */
    public function __construct(BackupDao $dao)
    {
        $this->dao = $dao;
    }
}
