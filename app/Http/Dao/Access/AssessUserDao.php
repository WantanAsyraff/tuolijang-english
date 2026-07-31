<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessUser;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 绩效考核人员关联
 * Class AssessUserDao.
 */
class AssessUserDao extends BaseDao
{
    use BatchSearchTrait;
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessUser::class;
    }
}
