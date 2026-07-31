<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\ApproveProcess;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 审核流程树
 * Class ApproveProcessDao.
 */
class ApproveProcessDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return ApproveProcess::class;
    }
}
