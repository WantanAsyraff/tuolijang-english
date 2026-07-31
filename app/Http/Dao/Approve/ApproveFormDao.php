<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\ApproveForm;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 审核流程表单
 * Class ApproveFormDao.
 */
class ApproveFormDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return ApproveForm::class;
    }
}
