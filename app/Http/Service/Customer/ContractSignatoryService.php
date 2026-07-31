<?php

declare(strict_types=1);


namespace App\Http\Service\Customer;

use App\Http\Dao\Customer\ContractSignatoryDao;
use crmeb\basic\BaseService;

/**
 * 合同签署人服务
 */
class ContractSignatoryService extends BaseService
{
    public function __construct(ContractSignatoryDao $dao)
    {
        $this->dao = $dao;
    }
}
