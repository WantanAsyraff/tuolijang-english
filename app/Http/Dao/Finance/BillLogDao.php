<?php

declare(strict_types=1);


namespace App\Http\Dao\Finance;

use App\Http\Model\Finance\BillLog;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class BillLogDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return BillLog::class;
    }
}
