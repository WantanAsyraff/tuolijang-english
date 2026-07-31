<?php

declare(strict_types=1);


namespace crmeb\services\wechat\contract;

/**
 * Interface ServeConfigInterface.
 */
interface ServeConfigInterface
{
    /**
     * @param null $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null);
}
