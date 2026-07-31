<?php

declare(strict_types=1);


namespace App\Http\Contract\Approve;

interface ApproveApplyInterface
{
    /**
     * 获取列表.
     */
    public function getList(array $where, array $field = ['*'], $sort = 'id', array $with = [], int $page = 0, int $limit = 0): array;

    /**
     * 获取详情.
     */
    public function getInfo(int $id, array $other = []): mixed;
}
