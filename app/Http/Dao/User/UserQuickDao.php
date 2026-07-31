<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserQuick;
use crmeb\basic\BaseDao;

class UserQuickDao extends BaseDao
{
    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserQuick::class;
    }
}
