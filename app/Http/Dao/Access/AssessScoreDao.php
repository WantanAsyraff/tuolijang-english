<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\AssessScore;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

/**
 * 绩效考核分数设置
 * Class AssessUserDao.
 */
class AssessScoreDao extends BaseDao
{
    use BatchSearchTrait;
    use ListSearchTrait;
    use TogetherSearchTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return AssessScore::class;
    }
}
