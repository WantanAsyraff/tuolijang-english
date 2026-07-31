<?php

declare(strict_types=1);


namespace App\Http\Service\Assess;

use App\Http\Dao\Access\AssessUserDao;
use crmeb\basic\BaseService;

/**
 * 绩效考核人员关联
 * Class AssessUserService.
 */
class AssessUserService extends BaseService
{
    public function __construct(AssessUserDao $dao)
    {
        $this->dao = $dao;
    }
}
