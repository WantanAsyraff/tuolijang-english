<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 用户日程表
 * Class UserSchedule.
 */
class UserScheduleRecord extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'user_schedule_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'schedultid' => 'integer',
        'status'     => 'integer',
        'remind_day' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return mixed
     */
    public function scopeUid($query, $value)
    {
        if ($value !== '') {
            return $query->where('uid', $value);
        }
    }

    /**
     * @return mixed
     */
    public function scopeSchedultid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('schedultid', $value);
        } elseif ($value !== '') {
            return $query->where('schedultid', $value);
        }
    }

    public function scopeRemindDay($query, $value)
    {
        if ($value !== '') {
            $query->where('remind_day', $value);
        }
    }
}
