<?php

declare(strict_types=1);


namespace crmeb\services\synchro;

/**
 * Class HelpCenter.
 */
class HelpCenter extends TokenService
{
    protected string $cacheTokenPrefix = '';

    protected string $salt = '';

    protected string $serviceName = '';

    protected string $aggregateSearchApi = '/api/v2/help_center/aggregate_search';

    protected string $sidebarSearchApi = '/api/v2/help_center/sidebar_search';

    /**
     * 结果页搜索.
     */
    public function aggregateSearch(array $data): mixed
    {
        return $this->httpRequest($this->aggregateSearchApi, $data);
    }

    /**
     * 侧边栏搜索.
     */
    public function sidebarSearch(array $data): mixed
    {
        return $this->httpRequest($this->sidebarSearchApi, $data);
    }
}
