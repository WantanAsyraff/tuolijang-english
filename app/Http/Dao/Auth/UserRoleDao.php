<?php

declare(strict_types=1);


namespace App\Http\Dao\Auth;

use App\Http\Model\Auth\UserRole;
use crmeb\basic\BaseDao;

/**
 * 企业成员角色权限
 * Class UserRoleDao.
 */
class UserRoleDao extends BaseDao
{
    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserRole::class;
    }
}
