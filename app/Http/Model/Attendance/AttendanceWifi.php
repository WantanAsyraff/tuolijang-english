<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;

/**
 * 考勤组wifi配置.
 */
class AttendanceWifi extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'attendance_wifi';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'group_id'   => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeMac($query, $value)
    {
        $query->where(function ($q) use ($value) {
            return $q->where('mac', $value)->orWhere('mac', strtoupper($value));
        });
    }
}
