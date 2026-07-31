<?php

declare(strict_types=1);


namespace App\Http\Dao\Frame;

use App\Http\Model\Frame\FrameScope;
use crmeb\basic\BaseDao;

/**
 * 管理范围.
 */
class FrameUserScopeDao extends BaseDao
{
    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return FrameScope::class;
    }
}
