<?php

declare(strict_types=1);


namespace App\Http\Model\Schedule;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 日程类型表.
 */
class ScheduleType extends BaseModel
{
    use TimeDataTrait;

    protected $appends = ['is_system_owned'];

    public function getIsSystemOwnedAttribute(): int
    {
        return (int) $this->id >= 1 && (int) $this->id <= 5 ? 1 : 0;
    }

    /**
     * 表名.
     * @var string
     */
    protected $table = 'schedule_type';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'entid'      => 'integer',
        'sort'       => 'integer',
        'is_public'  => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeUidLike($query, $value)
    {
        if ($value) {
            $query->where(fn ($q) => $q->orWhere('uid', $value)->orWhere('uid', ''));
        }
    }

    public function scopeUseridLike($query, $value)
    {
        if ($value) {
            $query->where(fn ($q) => $q->orWhere('user_id', $value)->orWhere('user_id', 0));
        }
    }

    public function scopeEntLike($query, $value)
    {
        if ($value) {
            $query->where(fn ($q) => $q->orWhere('entid', $value)->orWhere('entid', 0));
        }
    }
}
