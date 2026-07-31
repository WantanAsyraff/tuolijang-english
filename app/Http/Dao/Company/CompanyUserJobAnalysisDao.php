<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use App\Http\Model\Company\UserJobAnalysis;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 企业用户工作分析
 * Class CompanyUserJobAnalysisDao.
 */
class CompanyUserJobAnalysisDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     *
     * @return string
     */
    protected function setModel()
    {
        return UserJobAnalysis::class;
    }
}
