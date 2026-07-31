<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use App\Http\Model\Company\Company;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 企业管理Dao.
 */
class CompanyDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Company::class;
    }
}
