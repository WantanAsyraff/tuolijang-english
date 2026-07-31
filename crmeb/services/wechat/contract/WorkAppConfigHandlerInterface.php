<?php

declare(strict_types=1);


namespace crmeb\services\wechat\contract;

/**
 * 企业微信获取应用配置
 * Interface WorkAppConfigHandlerInterface.
 */
interface WorkAppConfigHandlerInterface
{
    /**
     * 获取应用配置.
     * @param string $type 应用标识
     */
    public function getAppConfig(string $corpId, string $type): array;
}
