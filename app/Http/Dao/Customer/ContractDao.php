<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\Contract;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 合同Dao
 */
class ContractDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return Contract::class;
    }
}
