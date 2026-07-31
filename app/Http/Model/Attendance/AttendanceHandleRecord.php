<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 处理记录.
 */
class AttendanceHandleRecord extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'attendance_handle_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'                     => 'integer',
        'statistics_id'          => 'integer',
        'shift_number'           => 'integer',
        'before_status'          => 'integer',
        'before_location_status' => 'integer',
        'after_status'           => 'integer',
        'after_location_status'  => 'integer',
        'source'                 => 'integer',
        'uid'                    => 'integer',
        'created_at'             => 'datetime:Y-m-d H:i:s',
        'updated_at'             => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对一关联用户.
     * @return HasOne
     */
    public function card()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'uid', 'name', 'avatar', 'phone']);
    }
}
