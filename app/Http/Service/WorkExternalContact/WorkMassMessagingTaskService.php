<?php

declare(strict_types=1);


namespace App\Http\Service\WorkExternalContact;

use App\Http\Dao\WorkExternalContact\WorkMassMessagingTaskDao;
use crmeb\basic\BaseDao;
use crmeb\basic\BaseService;

/**
 * 群发消息发送任务.
 * @mixin BaseDao
 */
class WorkMassMessagingTaskService extends BaseService
{
    public function __construct(WorkMassMessagingTaskDao $dao)
    {
        $this->dao = $dao;
    }
}
