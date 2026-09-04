<?php

declare(strict_types=1);


namespace App\Http\Model\Approve;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 假期类型表
 * Class ApproveHolidayType.
 */
class ApproveHolidayType extends BaseModel
{
    use SoftDeletes;

    protected $appends = ['is_system_owned'];

    public function getIsSystemOwnedAttribute(): int
    {
        $systemNames = ['事假', '年假', '陪产假', '调休', '产假', '丧假'];
        return (int) $this->getAttribute('id') >= 1
            && (int) $this->getAttribute('id') <= 6
            && in_array((string) $this->getAttribute('name'), $systemNames, true) ? 1 : 0;
    }

    /**
     * 表名.
     * @var string
     */
    protected $table = 'approve_holiday_type';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'                       => 'integer',
        'new_employee_limit'       => 'integer',
        'new_employee_limit_month' => 'integer',
        'duration_type'            => 'integer',
        'duration_calc_type'       => 'integer',
        'sort'                     => 'integer',
        'created_at'               => 'datetime:Y-m-d H:i:s',
        'updated_at'               => 'datetime:Y-m-d H:i:s',
        'deleted_at'               => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = ['deleted_at'];

    /**
     * id作用域
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
     * name作用域.
     */
    public function scopeNameLike($query, $value): void
    {
        if ($value !== '') {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * id作用域
     */
    public function scopeNotId($query, $value): void
    {
        if ($value !== '') {
            $query->where('id', '<>', $value);
        }
    }

    /**
     * deleted_at作用域
     */
    public function scopeFilterNormal($query, $value): void
    {
        $query->whereNotNull('deleted_at');
    }
}
