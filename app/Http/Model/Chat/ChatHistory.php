<?php

declare(strict_types=1);


namespace App\Http\Model\Chat;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ChatHistory.
 */
class ChatHistory extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'                  => 'integer',
        'user_id'             => 'integer',
        'chat_application_id' => 'integer',
        'created_at'          => 'datetime:Y-m-d H:i:s',
        'updated_at'          => 'datetime:Y-m-d H:i:s',
        'deleted_at'          => 'datetime:Y-m-d H:i:s',
        'top_up'              => 'datetime:Y-m-d H:i:s',
        'is_show'             => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'chat_history';

    public function scopeNotId($query, $value)
    {
        if ($value) {
            $query->whereNotIn('id', $value);
        }
    }
}
