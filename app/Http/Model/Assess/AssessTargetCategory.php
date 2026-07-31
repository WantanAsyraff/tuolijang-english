<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;

/**
 * 指标模板分类
 * Class AssessTargetCategory.
 */
class AssessTargetCategory extends BaseModel
{
    /**
     * 自动写入时间关闭.
     * @var bool
     */
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_target_category';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'     => 'integer',
        'entid'  => 'integer',
        'pid'    => 'integer',
        'types'  => 'integer',
        'status' => 'integer',
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
     * types作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeTypes($query, $value)
    {
        if ($value !== '') {
            return $query->where('types', $value);
        }
    }

    /**
     * entid作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            return $query->where('entid', $value);
        }
    }
}
