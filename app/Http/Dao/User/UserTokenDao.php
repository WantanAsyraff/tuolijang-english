<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserToken;
use crmeb\basic\BaseDao;

class UserTokenDao extends BaseDao
{
    protected function setModel()
    {
        return UserToken::class;
    }
}
