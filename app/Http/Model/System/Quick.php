<?php

declare(strict_types=1);


namespace App\Http\Model\System;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 菜单.
 */
class Quick extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_quick';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'cid'        => 'integer',
        'sort'       => 'integer',
        'types'      => 'integer',
        'pc_show'    => 'integer',
        'uni_show'   => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 关联查询分类.
     * @return HasOne
     */
    public function cate()
    {
        return $this->hasOne(Category::class, 'id', 'cid');
    }

    public function scopeNotId($query, $value)
    {
        if ($value !== '') {
            $query->where('id', '<>', $value);
        }
    }

    public function scopeCid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('cid', $value);
        } elseif ($value !== '') {
            $query->where('cid', $value);
        }
    }

    public function scopeNameLike($query, $value)
    {
        if ($value !== '') {
            $query->where('name', 'LIKE', "%{$value}%");
        }
    }
}
