<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use crmeb\basic\BaseModel;

/**
 * 用户Token.
 */
class UserToken extends BaseModel
{
    protected $table = 'user_token';

    protected $primaryKey = 'id';

    protected $casts = [
        'refresh_expires_at'   => 'datetime:Y-m-d H:i:s',
        'refresh_last_used_at' => 'datetime:Y-m-d H:i:s',
        'refresh_revoked_at'   => 'datetime:Y-m-d H:i:s',
        'id'                   => 'integer',
        'fail_time'            => 'datetime:Y-m-d H:i:s',
        'created_at'           => 'datetime:Y-m-d H:i:s',
        'updated_at'           => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeUid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('uid', $value);
        } elseif ($value !== '') {
            $query->where('uid', $value);
        }
    }
}
