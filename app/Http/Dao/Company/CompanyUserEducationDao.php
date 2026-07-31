<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use App\Http\Model\Company\UserEducation;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class CompanyUserEducationDao.
 */
class CompanyUserEducationDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserEducation::class;
    }
}
