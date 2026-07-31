<?php

declare(strict_types=1);


namespace App\Http\Dao\Position;

use App\Http\Model\Position\Category;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 职级类别
 * Class PositionCategoryDao.
 */
class PositionCategoryDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return Category::class;
    }
}
