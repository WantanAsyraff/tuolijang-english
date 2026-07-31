<?php

declare(strict_types=1);


namespace App\Http\Service\Company;

use App\Http\Dao\Company\CompanyInviteDao;
use crmeb\basic\BaseService;

/**
 * 企业邀请.
 */
class CompanyInviteService extends BaseService
{
    public function __construct(CompanyInviteDao $dao)
    {
        $this->dao = $dao;
    }

    public function getInfo(string $uniqued)
    {
        if (! $uniqued) {
            throw $this->exception('');
        }
    }
}
