<?php

declare(strict_types=1);


namespace App\Http\Model\Schedule;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * 日程表.
 */
class ScheduleUser extends BaseModel
{
    use TimeDataTrait;

    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'schedule_user';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'uid'         => 'integer',
        'schedule_id' => 'integer',
        'is_master'   => 'integer',
    ];

    public function schedule()
    {
        return $this->hasOne(Schedule::class,'id','schedule_id');
    }
    public function scopeUid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('uid', $value);
        } else {
            $query->where('uid', $value);
        }
    }

    /**
     * schedule_id作用域
     */
    public function scopeScheduleId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('schedule_id', $value);
        } else{
            $query->where('schedule_id', $value);
        }
    }
    public function scopeTodo(Builder $query, $value)
    {
        $query->whereHas('schedule', function ($query) use ($value) {
            $query->whereIn('status', [0,1]);
        });
    }
}
