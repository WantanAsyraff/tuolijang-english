<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use App\Http\Model\Customer\Traits\CustomFormCasts;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 联系人
 * Class Liaison.
 */
class Liaison extends BaseModel
{
    use CustomFormCasts;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'uid'         => 'integer',
        'creator_uid' => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
        'deleted_at'  => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'customer_liaison';

    /**
     * eid 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeEid($query, $value): void
    {
        is_array($value) ? $query->whereIn('eid', $value) : $query->where('eid', $value);
    }

    /**
     * id 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeId($query, $value): void
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }

    /**
     * uid 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeUid($query, $value): void
    {
        is_array($value) ? $query->whereIn('uid', $value) : $query->where('uid', $value);
    }

    /**
     * not_id作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeNotId($query, $value): void
    {
        is_array($value) ? $query->whereNotIn('id', $value) : $query->where('id', '<>', $value);
    }

    public function scopeLiaisonTel($query, $value)
    {
        $query->where('liaison_tel', 'like', "%{$value}%");
    }

    public function scopeLiaisonName($query, $value)
    {
        $query->where('liaison_name', 'like', "%{$value}%");
    }
}
