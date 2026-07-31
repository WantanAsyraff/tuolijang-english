<?php

declare(strict_types=1);


namespace App\Http\Dao\Package;

use App\Http\Model\Package\UpgradeRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UpgradeRecordDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return UpgradeRecord::class;
    }
}
