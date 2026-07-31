<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 合同签署方
 * Class ContractSignatory.
 */
class ContractSignatory extends BaseModel
{
    use SoftDeletes;
    use TimeDataTrait;

    /**
     * @var string
     */
    protected $table = 'contract_signatory';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'cid'         => 'integer',
        'user_id'     => 'integer',
        'sign_time'   => 'datetime:Y-m-d H:i:s',
        'sign_status' => 'integer',
        'types'       => 'integer',
        'deleted_at'  => 'datetime:Y-m-d H:i:s',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id')->select(['id', 'uid', 'name', 'avatar', 'phone']);
    }

    public function scopeCid($query, $value)
    {
        is_array($value) ? $query->whereIn('cid', $value) : $query->where('cid', $value);
    }
}
