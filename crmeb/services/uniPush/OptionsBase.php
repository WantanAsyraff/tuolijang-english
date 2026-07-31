<?php

declare(strict_types=1);


namespace crmeb\services\uniPush;

use crmeb\services\uniPush\helper\Str;

/**
 * Class OptionsBase.
 */
abstract class OptionsBase
{
    /**
     * @return array
     */
    public function toArray()
    {
        $publicData = get_object_vars($this);
        $data       = [];
        foreach ($publicData as $key => $value) {
            $data[Str::snake($key)] = $value;
        }
        return $data;
    }

    /**
     * 获取参数.
     * @return null|mixed
     */
    public function get(string $key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * 设置参数.
     * @param mixed $value
     * @return $this|mixed
     */
    public function set(string $key, $value)
    {
        $this->{$key} = $value;
        return $this;
    }
}
