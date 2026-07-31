<?php

declare(strict_types=1);


namespace App\Http\Model\Admin;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminInfo extends BaseModel
{
    use SoftDeletes;
    use TimeDataTrait;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'admin_info';

    protected $casts = [
        'id'         => 'integer',
        'sex'        => 'integer',
        'age'        => 'integer',
        'marriage'   => 'integer',
        'type'       => 'integer',
        'work_years' => 'integer',
        'is_part'    => 'integer',
        'sort'       => 'integer',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 字段黑名单.
     *
     * @var array
     */
    protected $guarded = [];

    public function scopeType($query, $value)
    {
        is_array($value) ? $query->whereIn('type', $value) : $query->where('type', $value);
    }

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }
}
