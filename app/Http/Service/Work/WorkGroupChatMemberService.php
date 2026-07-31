<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkGroupChatMemberDao;
use crmeb\basic\BaseService;

/**
 * 群成员
 * Class WorkGroupChatMemberService.
 */
class WorkGroupChatMemberService extends BaseService
{
    public function __construct(WorkGroupChatMemberDao $dao)
    {
        $this->dao = $dao;
    }
}
