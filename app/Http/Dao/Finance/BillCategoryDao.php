<?php

declare(strict_types=1);


namespace App\Http\Dao\Finance;

use App\Http\Model\Finance\BillCategory;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 资金流水分类
 * Class BillCategoryDao.
 */
class BillCategoryDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return BillCategory::class;
    }
}
