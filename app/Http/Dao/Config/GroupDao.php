<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\Group;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 组合数据
 * Class GroupDao.
 */
class GroupDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Group::class;
    }
}
