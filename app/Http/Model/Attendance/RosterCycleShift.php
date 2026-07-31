<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 排班周期.
 */
class RosterCycleShift extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'roster_cycle_shift';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'cycle_id'   => 'integer',
        'shift_id'   => 'integer',
        'number'     => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * cycle_id 作用域
     */
    public function scopeCycleId($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('cycle_id', $value);
        } elseif ($value !== '') {
            $query->where('cycle_id', $value);
        }
    }
}
