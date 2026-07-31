<?php

declare(strict_types=1);


namespace App\Http\Contract\Frame;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * 企业部门.
 */
interface FrameInterface
{
    public function getDepartmentTreeList(array $where, array $field = ['*'], null|array|string $sort = null, array $with = []): array;

    public function createDepartment(array $data): BaseModel|Model;

    public function getDepartmentInfo(array $where, array $field = ['*'], array $with = []): array;

    public function updateDepartment(array $where, array $data): void;

    public function deleteDepartment(int $id): bool;
}
