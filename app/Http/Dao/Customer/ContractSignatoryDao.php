<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\ContractSignatory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class ContractSignatoryDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel(): string
    {
        return ContractSignatory::class;
    }
}
