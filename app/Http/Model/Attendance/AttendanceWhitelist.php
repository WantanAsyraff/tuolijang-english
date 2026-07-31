<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 考勤白名单.
 */
class AttendanceWhitelist extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'attendance_whitelist';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'type'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对一远程关联用户.
     * @return HasManyThrough
     */
    public function card(): HasOne
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'uid', 'name', 'avatar', 'phone']);
    }

    /**
     * uid 作用域
     */
    public function scopeUid($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('uid', $value);
        } elseif ($value !== '') {
            $query->where('uid', $value);
        }
    }
}
