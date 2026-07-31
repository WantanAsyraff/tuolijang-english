<?php

declare(strict_types=1);


namespace App\Http\Dao\Finance;

use App\Http\Model\Finance\Paytype;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class PaytypeDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return Paytype::class;
    }
}
