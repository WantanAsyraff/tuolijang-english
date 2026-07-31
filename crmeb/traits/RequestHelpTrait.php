<?php

declare(strict_types=1);


namespace crmeb\traits;

use crmeb\utils\Arr;

/**
 * Trait RequestHelpTrait.
 */
trait RequestHelpTrait
{
    /**
     * 获取POST请求的数据.
     */
    public function postMore(array $params, ?bool $suffix = null): array
    {
        return Arr::more($this->request(), $params, $suffix);
    }

    /**
     * 获取GET请求的数据.
     */
    public function getMore(array $params, ?bool $suffix = null): array
    {
        return Arr::more($this->request(), $params, $suffix, 'get');
    }

    /**
     * @return mixed
     */
    abstract protected function request();
}
