<?php

declare(strict_types=1);


namespace crmeb\traits\model;

/**
 * path字段序列化
 * Trait PathAttrTrait.
 * @property array $attributes
 */
trait PathAttrTrait
{
    /**
     * 格式化path字段.
     * @param mixed $value
     * @return false|string[]
     */
    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value ? '/' . implode('/', $value) . '/' : '';
    }

    /**
     * 格式化path字段.
     * @param mixed $value
     * @return false|string[]
     */
    public function getPathAttribute($value)
    {
        return $value ? array_map('intval', array_merge(array_filter(explode('/', $value)))) : [];
    }
}
