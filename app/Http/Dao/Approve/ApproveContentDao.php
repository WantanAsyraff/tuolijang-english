<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\ApproveContent;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 申请内容表
 * Class ApproveContentDao.
 */
class ApproveContentDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return ApproveContent::class;
    }
}
