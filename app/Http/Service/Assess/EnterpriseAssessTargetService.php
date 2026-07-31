<?php

declare(strict_types=1);


namespace App\Http\Service\Assess;

use App\Http\Dao\Access\AssessTargetDao;
use crmeb\basic\BaseService;

/**
 * 考核内容指标
 * Class EnterpriseAssessTargetService.
 */
class EnterpriseAssessTargetService extends BaseService
{
    public function __construct(AssessTargetDao $dao)
    {
        $this->dao = $dao;
    }
}
