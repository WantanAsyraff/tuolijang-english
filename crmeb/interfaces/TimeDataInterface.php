<?php

declare(strict_types=1);


namespace crmeb\interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface TimeDataInterface.
 */
interface TimeDataInterface
{
    /**
     * 设置时间查询字段.
     * @return $this
     */
    public function setTimeField(string $timeField);

    public function getTimeField(): string;

    /**
     * 时间查询作用域
     * @param mixed $value
     * @return mixed
     */
    public function scopeTime(Builder $query, $value);
}
