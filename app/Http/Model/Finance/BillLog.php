<?php

declare(strict_types=1);


namespace App\Http\Model\Finance;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 收支记账操作日志
 * Class BillLog.
 */
class BillLog extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'client_bill_log';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'           => 'integer',
        'entid'        => 'integer',
        'bill_list_id' => 'integer',
        'uid'          => 'integer',
        'type'         => 'integer',
        'created_at'   => 'datetime:Y-m-d H:i:s',
        'updated_at'   => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * ID作用域
     */
    public function scopeId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } elseif ($value !== '') {
            $query->where('id', $value);
        }
    }

    /**
     * operation字段转json.
     */
    public function setOperationAttribute($value)
    {
        $this->attributes['operation'] = json_encode($value);
    }

    /**
     * operation字段转回数组.
     * @return mixed
     */
    public function getOperationAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * 一对一关联用户.
     * @return HasOne
     */
    public function card()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }
}
