<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use App\Http\Model\Customer\Traits\CustomFormCasts;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商机.
 */
class Product extends BaseModel
{
    use CustomFormCasts;
    use SoftDeletes;
    use TimeDataTrait;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'pid'        => 'integer',
        'spec_type'  => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_product';

    public function scopeNameLike($query, $value)
    {
        $query->where('name', 'like', "%{$value}%");
    }

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }

    public function attr()
    {
        return $this->hasMany(ProductAttr::class, 'product_id', 'id');
    }

    public function attrValue()
    {
        return $this->hasMany(ProductAttrValue::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->hasMany(ProductCategory::class, 'id', 'pid');
    }
}
