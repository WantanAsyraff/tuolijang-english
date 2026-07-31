<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 群发消息发送任务.
 */
class WorkMassMessagingTask extends BaseModel
{
    use SoftDeletes;
    use TimeDataTrait;

    protected $table = 'work_mass_messaging_task';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'             => 'integer',
        'mass_id'        => 'integer',
        'uid'            => 'integer',
        'status'         => 'integer',
        'sum_count'      => 'integer',
        'not_send_count' => 'integer',
        'success_count'  => 'integer',
        'fail_count'     => 'integer',
        'types'          => 'integer',
        'fail_list'      => 'array',
        'send_time'      => 'datetime:Y-m-d H:i:s',
        'created_at'     => 'datetime:Y-m-d H:i:s',
        'updated_at'     => 'datetime:Y-m-d H:i:s',
        'deleted_at'     => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeTypes($query, $value)
    {
        is_array($value) ? $query->whereIn('types', $value) : $query->where('types', $value);
    }
}
