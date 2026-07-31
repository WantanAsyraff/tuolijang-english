<?php

declare(strict_types=1);


namespace App\Http\Contract\Notepad;

/**
 * 个人笔记.
 */
interface NotepadInterface
{
    /**
     * 获取列表.
     */
    public function getList(array $where, array $field = ['*'], array|string $sort = 'updated_at', array $with = []): array;

    /**
     * 保存数据.
     */
    public function saveData($data): bool;

    /**
     * 通过ID修改.
     */
    public function updateById(int $id, array $data): bool;

    /**
     * 获取记录详情.
     */
    public function getInfoById(int $id): array;

    /**
     * 分组数据.
     */
    public function getGroupList(array $where): array;

    /**
     * 移动端列表.
     */
    public function getListForApp(array $where, array $field = ['*'], array|string $sort = 'updated_at', array $with = []): array;
}
