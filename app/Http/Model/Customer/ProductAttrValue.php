<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use crmeb\basic\BaseModel;

/**
 * 产品属性值.
 */
class ProductAttrValue extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'ot_price'   => 'decimal:2',
        'price'      => 'decimal:2',
        'cost'       => 'decimal:2',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_product_attr_value';

    public function setDetailAttribute($value)
    {
        $this->attributes['detail'] = json_encode($value);
    }

    /**
     * @param mixed $value
     * @return array|mixed
     */
    public function getDetailAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function scopeUnique($query, $value)
    {
        is_array($value) ? $query->whereIn('unique', $value) : $query->where('unique', $value);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function scopeProductId($query, $value)
    {
        is_array($value) ? $query->whereIn('product_id', $value) : $query->where('product_id', $value);
    }

    public function scopeAttrLike($query, $value)
    {
        $query->where('sku', 'like', '%' . $value . '%');
    }
}
