<?php

declare(strict_types=1);


namespace crmeb\traits;

/**
 * Trait CateControllerTrait.
 */
trait CateControllerTrait
{
    /**
     * Request字段获取.
     */
    protected function getRequestFields(): array
    {
        return [
            ['path', []],
            ['pid', 0],
            ['cate_name', ''],
            ['pic', ''],
            ['sort', 0],
            ['is_show', 1],
        ];
    }

    /**
     * 搜索字段.
     */
    protected function getSearchField(): array
    {
        return [
            ['cate_name', ''],
            ['type', ''],
            ['pid', 0],
            ['is_show', ''],
        ];
    }
}
