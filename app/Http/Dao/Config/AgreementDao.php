<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\Agreement;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class AgreementDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return Agreement::class;
    }
}
