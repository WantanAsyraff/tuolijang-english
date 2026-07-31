<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserCardPerfect;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserCardPerfectDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return UserCardPerfect::class;
    }
}
