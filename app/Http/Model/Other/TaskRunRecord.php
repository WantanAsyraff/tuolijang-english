<?php

declare(strict_types=1);


namespace App\Http\Model\Other;

use crmeb\basic\BaseModel;

/**
 * 任务执行记录
 * Class TaskRunRecord.
 */
class TaskRunRecord extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'task_run_record';

    /**
     * 主键id.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'task_id'    => 'integer',
        'line'       => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
