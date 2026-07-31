<?php

declare(strict_types=1);


namespace crmeb\services\wps\options;

use crmeb\interfaces\OptionsInterface;
use crmeb\traits\OptionsTrait;
use Illuminate\Support\Str;

/**
 * 公用
 * Class OfficeCommonOptions.
 */
class OfficeCommonOptions implements OptionsInterface
{
    use OptionsTrait;

    /**
     * @var array
     */
    public $common = [];

    /**
     * OfficeCommonOptions constructor.
     */
    public function __construct(array $common = [])
    {
        $this->common = $common;
    }

    /**
     * 设置属性.
     * @param mixed $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->common[$name] = $value;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->common as $key => $value) {
            $data[Str::snake($key)] = $value;
        }
        return $data;
    }
}
