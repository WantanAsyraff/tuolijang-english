<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use App\Http\Model\Company\UserWork;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserWorkDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserWork::class;
    }
}
