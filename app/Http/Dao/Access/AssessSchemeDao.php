<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessScheme;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 企业绩效考核方案
 * Class AssessSchemeDao.
 */
class AssessSchemeDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessScheme::class;
    }
}
