<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * 参数设置接口
 * Interface OptionsInterface.
 */
interface OptionsInterface
{
    /**
     * 对象转数组.
     */
    public function toArray(): array;

    /**
     * 获取参数.
     * @return mixed
     */
    public function get(string $key);

    /**
     * 设置参数.
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value);
}
