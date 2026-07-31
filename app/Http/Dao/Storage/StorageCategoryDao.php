<?php

declare(strict_types=1);


namespace App\Http\Dao\Storage;

use App\Http\Model\Storage\StorageCategory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class StorageCategoryDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel()
    {
        return StorageCategory::class;
    }
}
