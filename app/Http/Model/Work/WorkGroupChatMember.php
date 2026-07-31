<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 群成员表.
 */
class WorkGroupChatMember extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'               => 'integer',
        'group_id'         => 'integer',
        'type'             => 'integer',
        'join_time'        => 'integer',
        'join_scene'       => 'integer',
        'status'           => 'integer',
        'chat_sum'         => 'integer',
        'retreat_chat_num' => 'integer',
        'created_at'       => 'datetime:Y-m-d H:i:s',
        'updated_at'       => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_group_chat_member';
}
