<?php

declare(strict_types=1);


namespace App\Http\Model\System;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 省市区.
 */
class City extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_city';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'        => 'integer',
        'city_id'   => 'integer',
        'level'     => 'integer',
        'parent_id' => 'integer',
        'is_show'   => 'integer',
    ];

    /**
     * 获取子集分类查询条件.
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'city_id')->orderBy('id', 'ASC');
    }

    /**
     * name 作用域
     * @return mixed
     */
    public function scopeNameLike($query, $value)
    {
        if (is_array($value)) {
            $query->where(function ($query) use ($value) {
                foreach ($value as $val) {
                    $query->orWhere('name', 'like', '%' . $val . '%');
                }
            });
        } elseif ($value !== '') {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }
}
