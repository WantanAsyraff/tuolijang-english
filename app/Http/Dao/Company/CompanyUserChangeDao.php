<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use App\Http\Model\Company\UserChange;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class CompanyUserChangeDao extends BaseDao
{
    use ListSearchTrait;

    public function setModel()
    {
        return UserChange::class;
    }
}
