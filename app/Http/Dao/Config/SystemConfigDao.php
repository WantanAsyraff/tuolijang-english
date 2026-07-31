<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\Config;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 系统配置
 * Class SystemConfigDao.
 */
class SystemConfigDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Config::class;
    }
}
