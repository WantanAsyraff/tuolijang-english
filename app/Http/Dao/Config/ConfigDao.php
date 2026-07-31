<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Company\CompanyConfig;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 企业系统配置
 * Class ConfigDao.
 */
class ConfigDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return CompanyConfig::class;
    }
}
