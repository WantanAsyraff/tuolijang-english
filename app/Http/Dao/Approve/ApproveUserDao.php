<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\ApproveUser;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

/**
 * 审批流程人员
 * Class ApproveUserDao.
 */
class ApproveUserDao extends BaseDao
{
    use ListSearchTrait;
    use TogetherSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return ApproveUser::class;
    }
}
