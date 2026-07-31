<?php

declare(strict_types=1);


namespace App\Http\Dao\Other;

use App\Http\Model\Other\ExportRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class ExportRecordDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return ExportRecord::class;
    }
}
