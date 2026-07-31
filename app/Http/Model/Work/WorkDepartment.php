<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 部门表.
 */
class WorkDepartment extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'            => 'integer',
        'department_id' => 'integer',
        'parentid'      => 'integer',
        'sort'          => 'integer',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_department';
}
