<?php

declare(strict_types=1);


namespace App\Http\Model\Message;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 消息模板
 * Class MessageTemplate.
 */
class MessageTemplate extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'message_template';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'              => 'integer',
        'relation_id'     => 'integer',
        'message_id'      => 'integer',
        'type'            => 'integer',
        'status'          => 'integer',
        'relation_status' => 'integer',
        'push_rule'       => 'integer',
        'minute'          => 'integer',
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
        'deleted_at'      => 'datetime:Y-m-d H:i:s',
        'crud_event_id'   => 'integer',
    ];
}
