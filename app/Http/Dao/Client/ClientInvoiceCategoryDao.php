<?php

declare(strict_types=1);


namespace App\Http\Dao\Client;

use App\Http\Model\Customer\ClientInvoiceCategory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class ClientInvoiceCategoryDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ClientInvoiceCategory::class;
    }
}
