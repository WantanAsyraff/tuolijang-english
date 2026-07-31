<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;

/**
 * 考勤缺卡提醒.
 */
class AttendanceShortRemind extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'attendance_short_remind';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'shift_id'    => 'integer',
        'uid'         => 'integer',
        'short_type'  => 'integer',
        'work_time'   => 'datetime:Y-m-d H:i:s',
        'remind_time' => 'datetime:Y-m-d H:i:s',
        'is_push'     => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];
}
