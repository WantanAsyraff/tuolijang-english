<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessTarget;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class AssessTargetDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessTarget::class;
    }
}
