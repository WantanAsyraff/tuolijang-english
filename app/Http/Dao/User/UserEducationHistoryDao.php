<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserEducationHistory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class CompanyUserEducationDao.
 */
class UserEducationHistoryDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return UserEducationHistory::class;
    }
}
