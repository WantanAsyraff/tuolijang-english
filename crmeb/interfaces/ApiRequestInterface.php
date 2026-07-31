<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * api Request接口类
 * Interface ApiRequestInterface.
 */
interface ApiRequestInterface
{
    /**
     * 获取POST参数.
     */
    public function getMore(array $params, ?bool $suffix = null): array;

    /**
     * 获取GET参数.
     */
    public function postMore(array $params, ?bool $suffix = null): array;
}
