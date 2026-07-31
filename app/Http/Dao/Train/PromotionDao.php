<?php

declare(strict_types=1);


namespace App\Http\Dao\Train;

use App\Http\Model\Company\Promotion;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 晋升表Dao.
 * Class PromotionDao.
 */
class PromotionDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return Promotion::class;
    }
}
