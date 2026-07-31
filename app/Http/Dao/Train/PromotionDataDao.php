<?php

declare(strict_types=1);


namespace App\Http\Dao\Train;

use App\Http\Model\Company\PromotionData;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 晋升数据Dao.
 * Class PromotionDataDao.
 */
class PromotionDataDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return PromotionData::class;
    }
}
