<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserResume;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserResumeDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserResume::class;
    }
}
