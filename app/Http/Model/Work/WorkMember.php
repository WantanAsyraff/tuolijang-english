<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

class WorkMember extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'              => 'integer',
        'uid'             => 'integer',
        'gender'          => 'integer',
        'enable'          => 'integer',
        'is_leader'       => 'integer',
        'hide_mobile'     => 'integer',
        'main_department' => 'integer',
        'status'          => 'integer',
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_member';

    public function scopeUserid($query, $value)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('userid', $value);
            } else {
                $query->where('userid', $value);
            }
        }
    }
}
