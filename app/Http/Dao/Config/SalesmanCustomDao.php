<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\SalesmanCustom;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 业务自定义数据 Dao
 * Class SalesmanCustomDao.
 */
class SalesmanCustomDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return SalesmanCustom::class;
    }
}
