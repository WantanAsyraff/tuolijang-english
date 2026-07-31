<?php

declare(strict_types=1);


namespace crmeb\traits\dao;

use crmeb\basic\BaseDao;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;

/**
 * 聚合查询
 * Trait TogetherSearchTrait.
 * @mixin BaseDao
 */
trait TogetherSearchTrait
{
    /**
     * 获取某字段最大值
     * @param array|int|string $where
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function max($where, string $field)
    {
        return $this->search($where)->max($field);
    }

    /**
     * 获取某个字段平均值
     * @param array|int|string $where
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function avg($where, string $field)
    {
        return $this->search($where)->avg($field);
    }

    /**
     * 获取某个字段最小值
     * @param array|int|string $where
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function min($where, string $field)
    {
        return $this->search($where)->min($field);
    }

    /**
     * 获取某个字段总和.
     * @param array|int|string $where
     * @return int|mixed
     * @throws BindingResolutionException
     */
    public function sum($where, array|string $field, float $default = 0.00)
    {
        if (is_array($field)) {
            $query   = $this->search($where);
            $selects = array_map(function ($item) {
                return DB::raw("SUM(`{$item}`) AS `{$item}`");
            }, $field);
            $result = $query->select($selects)->first();
            $sums   = [];
            foreach ($field as $value) {
                $sums[$value] = (float) ($result->{$value} ?? $default);
            }
            return $sums;
        }
        return $this->search($where)->sum($field) ?: $default;
    }
}
