<?php

declare(strict_types=1);


namespace App\Http\Model\Chat;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;

class ChatAppAuth extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'      => 'integer',
        'app_id'  => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'chat_app_auth';

    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }
}
