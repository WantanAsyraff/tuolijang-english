<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\Resource;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 订单附件
 * Class ResourceDao.
 */
class ResourceDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return Resource::class;
    }
}
