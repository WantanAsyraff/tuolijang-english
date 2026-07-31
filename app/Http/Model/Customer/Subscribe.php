<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use App\Http\Model\Client\Customer;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 客户关注
 * Class ClientSubscribe.
 */
class Subscribe extends BaseModel
{
    use TimeDataTrait;

    /**
     * @var string
     */
    protected $table = 'client_subscribe';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'               => 'integer',
        'entid'            => 'integer',
        'uid'              => 'integer',
        'eid'              => 'integer',
        'types'            => 'integer',
        'subscribe_status' => 'integer',
        'created_at'       => 'datetime:Y-m-d H:i:s',
        'updated_at'       => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对一关联客户.
     * @return HasOne
     */
    public function client()
    {
        return $this->hasOne(Customer::class, 'id', 'eid')->select([
            'customer.id',
            'customer.customer_name as name',
        ]);
    }

    /**
     * 客户ID作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeEid($query, $value)
    {
        is_array($value) ? $query->whereIn('eid', $value) : $query->where('eid', $value);
    }

    /**
     * 用户ID作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeUid($query, $value)
    {
        is_array($value) ? $query->whereIn('uid', $value) : $query->where('uid', $value);
    }
}
