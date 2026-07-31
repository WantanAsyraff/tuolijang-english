<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessTarget;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

/**
 * 指标模板记录
 * Class PositionDao.
 */
class EnterpriseTargetDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;
    use TogetherSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessTarget::class;
    }
}
