<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 业务自定义数据.
 */
class SalesmanCustom extends BaseModel
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $table = 'salesman_custom_field';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * id作用域
     * @param Builder $query
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
     * custom_type 作用域
     * @param Builder $query
     */
    public function scopeCustomType($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('custom_type', $value);
        } elseif ($value !== '') {
            $query->where('custom_type', $value);
        }
    }

    /**
     * field_list 修改器.
     */
    protected function setFieldListAttribute($value): void
    {
        $this->attributes['field_list'] = $value ? json_encode($value) : '';
    }

    /**
     * field_list 获取器.
     */
    protected function getFieldListAttribute($value): mixed
    {
        return $value ? json_decode($value, true) : [];
    }
}
