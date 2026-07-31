<?php

declare(strict_types=1);


namespace App\Http\Dao\Company;

use crmeb\basic\BaseDao;

/**
 * 企业链接邀请用户.
 */
class CompanyInviteDao extends BaseDao
{
    protected function setModel()
    {
        return CompanyInviteDao::class;
    }
}
