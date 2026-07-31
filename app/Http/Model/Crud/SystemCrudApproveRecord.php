<?php

declare(strict_types=1);


namespace App\Http\Model\Crud;

use crmeb\basic\BaseModel;

/**
 * 低代码审批数据记录.
 * class SystemCrudApproveRecord
 */
class SystemCrudApproveRecord extends BaseModel
{
    /**
     * 表明.
     * @var string
     */
    protected $table = 'system_crud_approve_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'approve_id' => 'integer',
        'crud_id'    => 'integer',
        'data_id'    => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }


    public function getOriginalDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getOriginalScheduleDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

}
