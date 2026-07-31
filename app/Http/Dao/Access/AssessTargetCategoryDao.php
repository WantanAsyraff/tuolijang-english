<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessTargetCategory;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 指标、指标模板分类
 * Class AssessTargetCategoryDao.
 */
class AssessTargetCategoryDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessTargetCategory::class;
    }
}
