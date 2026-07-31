<?php

declare(strict_types=1);


namespace App\Http\Contract\Package;

interface HelpCenterInterface
{
    /**
     * 结果页搜索.
     */
    public function aggregateSearch(array $where): mixed;

    /**
     * 侧边栏搜索.
     */
    public function sidebarSearch(array $where): mixed;
}
