<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 排班周期.
 */
class RosterCycle extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'roster_cycle';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'group_id'   => 'integer',
        'cycle'      => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 远程一对多考勤班次
     * @return HasManyThrough
     */
    public function shifts()
    {
        return $this->hasManyThrough(
            AttendanceShift::class,
            RosterCycleShift::class,
            'cycle_id',
            'id',
            'id',
            'shift_id'
        )->orderBy('roster_cycle_shift.number')->select(['attendance_shift.id', 'attendance_shift.name', 'attendance_shift.color']);
    }

    /**
     * ID作用域
     */
    public function scopeId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } elseif ($value !== '') {
            $query->where('id', $value);
        }
    }
}
