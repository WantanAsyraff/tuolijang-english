<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CompanyConfig.
 */
class CompanyConfig extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_config';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'upload_type' => 'integer',
        'width'       => 'integer',
        'high'        => 'integer',
        'sort'        => 'integer',
        'entid'       => 'integer',
        'is_show'     => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * value修改器.
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode($value);
    }

    /**
     * parameter 获取器.
     */
    public function getParameterAttribute($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * value获取器.
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * 配置动态作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeKey($query, $value)
    {
        if (is_array($value)) {
            return $query->whereIn('key', $value);
        }
        if ($value) {
            return $query->where('key', $value);
        }
    }

    /**
     * 配置动态作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeEntid($query, $value)
    {
        if ($value) {
            return $query->where('entid', $value);
        }
    }

    /**
     * 分类动态作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeCategory($query, $value)
    {
        if ($value) {
            return $query->where('category', $value);
        }
    }
}
