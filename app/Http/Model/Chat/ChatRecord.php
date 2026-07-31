<?php

declare(strict_types=1);


namespace App\Http\Model\Chat;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRecord extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'                   => 'integer',
        'chat_history_id'      => 'integer',
        'vote_status'          => 'integer',
        'prompt_tokens'        => 'integer',
        'completion_tokens'    => 'integer',
        'tokens'               => 'integer',
        'run_time'             => 'integer',
        'created_at'           => 'datetime:Y-m-d H:i:s',
        'updated_at'           => 'datetime:Y-m-d H:i:s',
        'deleted_at'           => 'datetime:Y-m-d H:i:s',
        'uid'                  => 'integer',
        'is_show'              => 'integer',
        'chat_applications_id' => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'chat_record';
}
