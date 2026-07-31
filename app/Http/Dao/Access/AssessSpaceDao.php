<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessSpace;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class AssessSpaceDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessSpace::class;
    }
}
