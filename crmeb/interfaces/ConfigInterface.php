<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * 配置基础类
 * Interface ConfigInterface.
 */
interface ConfigInterface
{
    /**
     * 获取单个配置.
     * @return mixed
     */
    public function getConfig(string $name);

    /**
     * 获取多个配置.
     */
    public function getConfigs(array $name): array;

    /**
     * 获取配置分页的配置.
     * @return mixed
     */
    public function getConfigLimit(string $name, int $limit = 0, int $entid = 0, int $page = 0);
}
