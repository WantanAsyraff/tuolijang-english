<?php

declare(strict_types=1);


namespace App\Http\Model\Program;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 项目成员
 * Class ProgramMember.
 */
class ProgramMember extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'program_member';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'program_id' => 'integer',
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
     * program_id作用域
     */
    public function scopeProgramId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('program_id', $value);
        } else {
            $query->where('program_id', $value);
        }
    }
}
