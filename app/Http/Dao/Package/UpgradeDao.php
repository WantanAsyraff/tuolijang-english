<?php

declare(strict_types=1);


namespace App\Http\Dao\Package;

use App\Http\Model\Package\Upgrade;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UpgradeDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Upgrade::class;
    }
}
