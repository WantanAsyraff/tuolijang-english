<?php

declare(strict_types=1);


namespace crmeb\services\wechat\config;

use crmeb\services\wechat\contract\ConfigHandlerInterface;
use crmeb\services\wechat\contract\ServeConfigInterface;
use crmeb\services\wechat\DefaultConfig;

/**
 * Http请求配置
 * Class HttpCommonConfig.
 */
class HttpCommonConfig implements ConfigHandlerInterface
{
    /**
     * @var bool[]
     */
    protected array $config = [
        'verify'  => false,
        'timeout' => 5,
    ];

    protected string $serve;

    /**
     * @return $this
     */
    public function setServe(string $serve): static
    {
        $this->serve = $serve;
        return $this;
    }

    /**
     * 获取服务端实例.
     * @return ServeConfigInterface
     */
    public function getServe()
    {
        return app()->get($this->serve);
    }

    /**
     * 直接获取配置.
     * @param null $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        if ($value = DefaultConfig::value($key)) {
            return $value;
        }

        return $this->getServe()->getConfig(DefaultConfig::key($key), $default);
    }

    public function all(): array
    {
        return $this->config;
    }
}
