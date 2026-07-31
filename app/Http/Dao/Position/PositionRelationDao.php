<?php

declare(strict_types=1);


namespace App\Http\Dao\Position;

use App\Http\Model\Position\Relation;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 职级体系
 * Class PositionRelationDao.
 */
class PositionRelationDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return Relation::class;
    }
}
