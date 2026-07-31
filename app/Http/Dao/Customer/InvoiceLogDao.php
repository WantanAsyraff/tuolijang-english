<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\InvoiceLog;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class InvoiceLogDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return InvoiceLog::class;
    }
}
