<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use crmeb\basic\BaseModel;

/**
 * 快捷回复分组.
 */
class WorkReplyTempGroup extends BaseModel
{
    protected $table = 'work_reply_temp_group';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'pid'        => 'integer',
        'uid'        => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeNameLike($query, $value)
    {
        $query->where('name', 'like', '%' . $value . '%');
    }
}
