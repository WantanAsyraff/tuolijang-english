<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\DictType;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 组合数据
 * Class GroupDao.
 */
class DictTypeDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return DictType::class;
    }
}
