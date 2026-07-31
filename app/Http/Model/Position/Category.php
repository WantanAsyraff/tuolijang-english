<?php

declare(strict_types=1);


namespace App\Http\Model\Position;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;

/**
 * 职级类别
 * Class Category.
 */
class Category extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'rank_category';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'number'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return HasOne
     */
    public function cate()
    {
        return $this->hasOne(self::class, 'id', 'pid');
    }

    /**
     * path修改器.
     */
    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value ? implode('/', $value) : '';
    }

    /**
     * path获取器.
     * @return false|string[]
     */
    public function getPathAttribute($value)
    {
        return $value ? array_map('intval', explode('/', $value)) : [];
    }

    /**
     * name作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeNameLike($query, $value)
    {
        if ($value) {
            return $query->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * id作用域
     * @param Builder $query
     */
    public function scopeId($query, $value)
    {
        if ($value !== '') {
            $query->where('id', $value);
        }
    }

    /**
     * entid作用域
     * @param Builder $query
     */
    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            $query->where('entid', $value);
        }
    }

    /**
     * ID 作用域
     */
    public function scopeNotId($query, $value): void
    {
        $query->where('id', '<>', $value);
    }
}
