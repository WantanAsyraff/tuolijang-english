<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * Resource Dao 接口
 * Interface ResourceDaoInterface.
 */
interface ResourceDaoInterface
{
    /**
     * 列表查询.
     * @param array|string[] $field
     * @param null $sort
     * @return mixed
     */
    public function getList(array $where, array $field = ['*'], int $page = 0, int $limit = 0, $sort = null, array $with = []);
}
