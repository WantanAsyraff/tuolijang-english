<?php

declare(strict_types=1);


namespace App\Http\Model\Program;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 项目版本
 * Class ProgramVersion.
 */
class ProgramVersion extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'program_version';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'program_id'  => 'integer',
        'creator_uid' => 'integer',
        'sort'        => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
        'deleted_at'  => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 隐藏字段.
     * @var string[]
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * id 作用域
     */
    public function scopeId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } elseif ($value !== '') {
            $query->where('id', $value);
        }
    }

    /**
     * creator_uid 作用域
     */
    public function scopeCreatorUid($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('creator_uid', $value);
        } elseif ($value !== '') {
            $query->where('creator_uid', $value);
        }
    }

    /**
     * program_id 作用域
     */
    public function scopeProgramId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('program_id', $value);
        } elseif ($value !== '') {
            $query->where('program_id', $value);
        }
    }
}
