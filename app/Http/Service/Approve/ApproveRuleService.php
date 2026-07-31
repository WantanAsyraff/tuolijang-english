<?php

declare(strict_types=1);


namespace App\Http\Service\Approve;

use App\Http\Dao\Approve\ApproveRuleDao;
use crmeb\basic\BaseService;

/**
 * 审核规则表.
 */
class ApproveRuleService extends BaseService
{
    public function __construct(ApproveRuleDao $dao)
    {
        $this->dao = $dao;
    }
}
