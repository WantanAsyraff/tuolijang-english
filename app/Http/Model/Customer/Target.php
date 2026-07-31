<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use crmeb\basic\BaseModel;

/**
 * 业绩目标.
 */
class Target extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'link_id'    => 'integer',
        'amount'     => 'decimal:2',
        'year'       => 'integer',
        'month'      => 'integer',
        'types'      => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_target';

    public function scopeLinkId($query, $value)
    {
        is_array($value) ? $query->whereIn('link_id', $value) : $query->where('link_id', $value);
    }
}
