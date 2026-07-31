<?php

declare(strict_types=1);


namespace App\Http\Service\Customer;

use App\Http\Dao\Client\CustomerProductAttrDao;
use App\Http\Dao\Customer\ProductAttrDao;
use crmeb\basic\BaseService;

/**
 * 产品属性service
 * @mixin ProductAttrDao
 */
class ProductAttrService extends BaseService
{
    public function __construct(ProductAttrDao $dao)
    {
        $this->dao = $dao;
    }
}
