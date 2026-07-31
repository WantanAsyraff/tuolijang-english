<?php

declare(strict_types=1);


namespace App\Http\Dao\Position;

use App\Http\Model\Position\Level;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

class PositionLevelDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected $each = false;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return Level::class;
    }
}
