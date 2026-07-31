<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkMemberOtherDao;
use crmeb\basic\BaseService;

class WorkMemberOtherService extends BaseService
{
    public function __construct(WorkMemberOtherDao $dao)
    {
        $this->dao = $dao;
    }
}
