<?php

declare(strict_types=1);


namespace App\Http\Dao\Cloud;

use App\Http\Model\Cloud\CloudViewHistory;
use crmeb\basic\BaseDao;

class CloudViewHistoryDao extends BaseDao
{
    protected function setModel()
    {
        return CloudViewHistory::class;
    }
}
