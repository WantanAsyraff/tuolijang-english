<?php

declare(strict_types=1);


namespace App\Http\Dao\Access;

use App\Http\Model\Assess\TemplateCollect;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

/**
 * 考核模板收藏
 * Class TemplateCollectDao.
 */
class TemplateCollectDao extends BaseDao
{
    use ListSearchTrait;
    use TogetherSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return TemplateCollect::class;
    }
}
