<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;

/**
 * 财务支付方式.
 */
class Paytype extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_paytype';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeNameLike($query, $value)
    {
        return $value !== '' ? $query->where('name', 'LIKE', "%{$value}%") : null;
    }
}
