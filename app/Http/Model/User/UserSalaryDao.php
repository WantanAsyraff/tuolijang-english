<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use App\Http\Model\Company\UserSalary;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserSalaryDao extends BaseDao
{
    use ListSearchTrait;

    public function setModel()
    {
        return UserSalary::class;
    }
}
