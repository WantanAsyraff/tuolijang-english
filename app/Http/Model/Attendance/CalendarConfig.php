<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;

/**
 * 日历设置.
 */
class CalendarConfig extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'calendar_config';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'is_rest'    => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * id 作用域
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
     * day 作用域
     */
    public function scopeYear($query, $value): void
    {
        if ($value !== '') {
            $query->whereYear('day', $value);
        }
    }

    /**
     * day 作用域
     */
    public function scopeMonth($query, $value): void
    {
        if ($value !== '') {
            $query->whereMonth('day', $value);
        }
    }

    /**
     * day 作用域
     */
    public function scopeDay($query, $value): void
    {
        if ($value !== '') {
            $query->whereDate('day', $value);
        }
    }
}
