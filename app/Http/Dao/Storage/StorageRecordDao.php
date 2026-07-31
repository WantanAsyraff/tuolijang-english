<?php

declare(strict_types=1);


namespace App\Http\Dao\Storage;

use App\Http\Model\Storage\StorageRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

/**
 * 物资记录Dao.
 * Class StorageDao.
 */
class StorageRecordDao extends BaseDao
{
    use ListSearchTrait;
    use TogetherSearchTrait;

    protected function setModel()
    {
        return StorageRecord::class;
    }
}
