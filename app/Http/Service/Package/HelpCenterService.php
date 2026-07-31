<?php

declare(strict_types=1);


namespace App\Http\Service\Package;

use App\Http\Contract\Package\HelpCenterInterface;
use crmeb\basic\BaseService;
use crmeb\services\synchro\HelpCenter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 帮助中心.
 */
class HelpCenterService extends BaseService implements HelpCenterInterface
{
    /**
     * 结果页搜索.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function aggregateSearch(array $where): mixed
    {
        return app()->get(HelpCenter::class)->aggregateSearch($where);
    }

    /**
     * 侧边栏搜索.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function sidebarSearch(array $where): mixed
    {
        return app()->get(HelpCenter::class)->sidebarSearch($where);
    }
}
