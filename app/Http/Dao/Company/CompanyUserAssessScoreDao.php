<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use App\Http\Model\Company\UserAssessScore;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 绩效考核分数变动
 * Class CompanyUserAssessScoreDao.
 */
class CompanyUserAssessScoreDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return string
     */
    protected function setModel()
    {
        return UserAssessScore::class;
    }
}
