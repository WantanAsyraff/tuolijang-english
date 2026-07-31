<?php

declare(strict_types=1);


namespace App\Http\Model\Message;

use crmeb\basic\BaseModel;

/**
 * App\Http\Models\message\MessageSubscribe.
 */
class MessageSubscribe extends BaseModel
{
    protected $table = 'message_subscribe';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'           => 'integer',
        'user_id'      => 'integer',
        'is_subscribe' => 'integer',
    ];

    protected function getMessageIdAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    protected function setMessageIdAttribute($value)
    {
        $this->attributes['message_id'] = $value ? json_encode($value) : '';
    }
}
