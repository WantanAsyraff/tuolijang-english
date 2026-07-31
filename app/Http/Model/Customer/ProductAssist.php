<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use crmeb\basic\BaseModel;

/**
 * 商机.
 */
class ProductAssist extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'product_id'  => 'integer',
        'price'       => 'decimal:2',
        'ot_price'    => 'decimal:2',
        'total_price' => 'decimal:2',
        'count'       => 'integer',
        'discount'    => 'integer',
        'link_id'     => 'integer',
        'link_type'   => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_product_assist';

    public function scopeUnique($query, $value)
    {
        is_array($value) ? $query->whereIn('unique', $value) : $query->where('unique', $value);
    }

    public function scopeLinkId($query, $value)
    {
        is_array($value) ? $query->whereIn('link_id', $value) : $query->where('link_id', $value);
    }

    public function scopeLinkType($query, $value)
    {
        is_array($value) ? $query->whereIn('link_type', $value) : $query->where('link_type', $value);
    }
}
