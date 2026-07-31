<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserEnterpriseInvite;
use crmeb\basic\BaseDao;

/**
 * 企业链接邀请用户
 * Class UserEnterpriseInviteDao.
 */
class UserEnterpriseInviteDao extends BaseDao
{
    protected function setModel()
    {
        return UserEnterpriseInvite::class;
    }
}
