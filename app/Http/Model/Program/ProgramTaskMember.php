<?php

declare(strict_types=1);


namespace App\Http\Model\Program;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 项目任务成员
 * Class ProgramTaskMember.
 */
class ProgramTaskMember extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'program_task_member';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'task_id'    => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * id作用域
     */
    public function scopeId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } else {
            $query->where('id', $value);
        }
    }

    /**
     * uid作用域
     */
    public function scopeUid($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('uid', $value);
        } else {
            $query->where('uid', $value);
        }
    }

    /**
     * task_id 作用域
     */
    public function scopeTaskId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('task_id', $value);
        } elseif ($value !== '') {
            $query->where('task_id', $value);
        }
    }
}
