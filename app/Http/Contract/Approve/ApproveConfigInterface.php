<?php

declare(strict_types=1);


namespace App\Http\Contract\Approve;

use Illuminate\Contracts\Container\BindingResolutionException;

interface ApproveConfigInterface
{
    /**
     * 获取审批配置列表.
     */
    public function getList(array $where, array $field = ['*'], array|string $sort = 'id', array $with = []): array;

    /**
     * 获取详情.
     */
    public function getInfo(int $id): array;

    /**
     * 保存配置信息.
     * @throws BindingResolutionException
     */
    public function save(array $data): mixed;

    /**
     * 显示/隐藏配置状态
     */
    public function showUpdate(int $id, int $status = 0): int;
}
