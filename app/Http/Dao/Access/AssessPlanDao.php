<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessPlan;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class AssessPlanDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessPlan::class;
    }
}
