<?php

declare(strict_types=1);


namespace App\Http\Service\User;

use App\Http\Dao\User\UserRemindLogDao;
use crmeb\basic\BaseService;

/**
 * 用户消息提醒日志
 * Class UserRemindLogService.
 * @mixin UserRemindLogDao
 */
class UserRemindLogService extends BaseService
{
    public function __construct(UserRemindLogDao $dao)
    {
        $this->dao = $dao;
    }
}
