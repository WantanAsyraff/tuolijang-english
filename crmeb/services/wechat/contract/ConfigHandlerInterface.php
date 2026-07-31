<?php

declare(strict_types=1);


namespace crmeb\services\wechat\contract;

/**
 * 配置
 * Interface ConfigHandlerInterface.
 */
interface ConfigHandlerInterface
{
    /**
     * 获取全部.
     */
    public function all(): array;
}
