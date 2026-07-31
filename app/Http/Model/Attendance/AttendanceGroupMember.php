<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 考勤组人员.
 */
class AttendanceGroupMember extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'attendance_group_member';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'group_id'   => 'integer',
        'entid'      => 'integer',
        'member'     => 'integer',
        'type'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * member 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeMember($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('member', $value);
        } elseif ($value !== '') {
            $query->where('member', $value);
        }
    }

    /**
     * group_id 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeNotGroupId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereNotIn('group_id', $value);
        } elseif ($value !== '') {
            $query->where('group_id', '<>', [$value]);
        }
    }

    /**
     * ID作用域
     * @param mixed $query
     * @param mixed $value
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
     * type作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeType($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('type', $value);
        } elseif ($value !== '') {
            $query->where('type', $value);
        }
    }
}
