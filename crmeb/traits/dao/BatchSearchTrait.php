<?php

declare(strict_types=1);


namespace crmeb\traits\dao;

use crmeb\basic\BaseDao;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 批量操作搜索
 * Trait BatchSearchTrait.
 * @mixin BaseDao
 */
trait BatchSearchTrait
{
    /**
     * 插入数据.
     * @return bool
     * @throws BindingResolutionException
     */
    public function insert(array $data)
    {
        return $this->getModel(false)->insert($data);
    }
}
