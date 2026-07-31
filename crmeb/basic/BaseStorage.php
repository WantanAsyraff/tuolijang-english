<?php

declare(strict_types=1);


namespace crmeb\basic;

use crmeb\traits\ErrorTrait;

/**
 * Class BaseStorage.
 */
abstract class BaseStorage
{
    use ErrorTrait;

    /**
     * 驱动名称.
     */
    protected ?string $name;

    /**
     * 驱动配置文件名.
     */
    protected ?string $configFile;

    /**
     * BaseStorage constructor.
     * @param null|string $name 驱动名
     * @param array $config 其他配置
     * @param null|string $configFile 驱动配置名
     */
    public function __construct(?string $name = null, array $config = [], ?string $configFile = null)
    {
        $this->name       = $name;
        $this->configFile = $configFile;
        $this->initialize($config);
    }

    /**
     * 初始化.
     * @return mixed
     */
    abstract protected function initialize(array $config);
}
