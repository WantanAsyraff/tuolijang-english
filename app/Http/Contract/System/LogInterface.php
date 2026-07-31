<?php

declare(strict_types=1);


namespace App\Http\Contract\System;

/**
 * 日志.
 */
interface LogInterface
{
    /**
     * 获取列表(分页).
     */
    public function getLogPageList(array $where, int $page, int $limit, array $field, array|string $sort, array $with): array;

    /**
     * 新增日志记录.
     */
    public function createLog(string $userId, int $entId, string $userName, string $type): bool;

    /**
     * 新增已整理的日志记录.
     */
    public function createLogFromData(array $data): bool;

    /**
     * 清理过期日志.
     */
    public function deleteExpiredLogs(int $months = 12): int;
}
