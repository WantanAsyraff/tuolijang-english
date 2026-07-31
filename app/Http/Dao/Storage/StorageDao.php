<?php

declare(strict_types=1);


namespace App\Http\Dao\Storage;

use App\Http\Model\Storage\Storage;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 物资管理Dao.
 * Class StorageDao.
 */
class StorageDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return Storage::class;
    }
}
