<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use crmeb\basic\BaseModel;

/**
 * 用户消息提醒日志
 * Class UserRemindLog.
 */
class UserRemindLog extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'user_remind_log';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'entid'       => 'integer',
        'week'        => 'integer',
        'month'       => 'integer',
        'day'         => 'integer',
        'year'        => 'integer',
        'quarter'     => 'integer',
        'user_id'     => 'integer',
        'relation_id' => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];
}
