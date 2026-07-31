<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use crmeb\basic\BaseModel;

/**
 * 商机.
 */
class ProductAttr extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_product_attr';

    public function setAttrValuesAttribute($value)
    {
        $this->attributes['attr_values'] = implode('-!-', $value);
    }

    /**
     * @param mixed $value
     * @return array|mixed
     */
    public function getAttrValuesAttribute($value)
    {
        return $value ? explode('-!-', $value) : [];
    }
}
