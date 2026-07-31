<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\SystemStorage;
use crmeb\basic\BaseDao;
use crmeb\basic\BaseModel;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\HigherOrderWhenProxy;

/**
 * Class StorageDao.
 */
class SystemStorageDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return BaseModel|HigherOrderWhenProxy
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function search($where, ?bool $authWhere = null)
    {
        return parent::search($where, $authWhere)->when(isset($where['type']), function ($query) use ($where) {
            $query->where('type', $where['type']);
        })->where('is_delete', 0)->when(isset($where['access_key']), function ($query) use ($where) {
            $query->where('access_key', $where['access_key']);
        })->when(! empty($where['id']), function ($query) use ($where) {
            $query->where('id', $where['id']);
        });
    }

    protected function setModel(): string
    {
        return SystemStorage::class;
    }
}
