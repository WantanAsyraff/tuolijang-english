<?php

declare(strict_types=1);


namespace App\Http\Dao\Train;

use App\Http\Model\Company\HayGroupData;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 评估表数据Dao.
 * Class HayGroupDataDao.
 */
class HayGroupDataDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return HayGroupData::class;
    }
}
