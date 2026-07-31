<?php

declare(strict_types=1);


namespace App\Http\Contract\System;

interface PermissionInterface
{
    public function getUserList(int $entId, array $where = [], array $field = ['*'], array $with = []): array;

    public function getUserPermission(string $uuid, int $entId);

    public function saveUserPermission(string $uuid, int $entId);
}
