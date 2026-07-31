<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\Approve;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 审核流程表
 * Class ApproveDao.
 */
class ApproveDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Approve::class;
    }
}
