<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkMemberRelationDao;
use crmeb\basic\BaseService;

class WorkMemberRelationService extends BaseService
{
    public function __construct(WorkMemberRelationDao $dao)
    {
        $this->dao = $dao;
    }
}
