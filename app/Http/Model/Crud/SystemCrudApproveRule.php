<?php

declare(strict_types=1);


namespace App\Http\Model\Crud;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SystemCrudApproveRule extends BaseModel
{
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'approve_id'  => 'integer',
        'abnormal'    => 'integer',
        'recall'      => 'integer',
        'is_sign'     => 'integer',
        'is_transfer' => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'system_crud_approve_rule';

    /**
     * 修改权限.
     * @return mixed
     */
    public function getEditAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 修改权限.
     * @return mixed
     */
    public function setEditAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['edit'] = json_encode($value);
        } else {
            $this->attributes['edit'] = json_encode(array_map('intval', explode(',', $value)));
        }
    }

    /**
     * 一对一关联用户.
     * @return HasOne
     */
    public function abCard()
    {
        return $this->hasOne(Admin::class, 'id', 'abnormal');
    }

    /**
     * 一对一关联.
     * @return HasOne
     */
    public function approve()
    {
        return $this->hasOne(SystemCrudApprove::class, 'id', 'approve_id');
    }
}
