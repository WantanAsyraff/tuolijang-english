<?php

declare(strict_types=1);


namespace crmeb\services\wps;

/**
 * Class Config.
 */
class Config
{
    /**
     * 配置字段.
     * @var string[]
     */
    protected $configField = [
        'appid'  => 'wps_appid',
        'appKey' => 'wps_appkey',
    ];

    protected $config = [];

    /**
     * 获取配置.
     * @return null|mixed
     */
    public function get(string $key)
    {
        return $this->getKeyValue($key);
    }

    /**
     * 设置配置.
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * 获取全部配置.
     * @return array
     */
    public function all()
    {
        foreach ($this->configField as $key => $value) {
            $this->getKeyValue($key);
        }
        return $this->config;
    }

    /**
     * 获取某个配置.
     * @return mixed
     */
    protected function getKeyValue(string $key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        if (isset($this->configField[$key])) {
            $this->config[$key] = sys_config($this->configField[$key]);
            return $this->config[$key];
        }
        return null;
    }
}
