<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;

/**
 * 云存储
 * Class SystemStorage.
 */
class SystemStorage extends BaseModel
{
    protected $table = 'system_storage';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'type'        => 'integer',
        'is_ssl'      => 'integer',
        'status'      => 'integer',
        'is_delete'   => 'integer',
        'add_time'    => 'integer',
        'update_time' => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeNameAttr($query, $value)
    {
        $query->where('name', $value);
    }

    /**
     * 类型搜索器.
     */
    public function scopeTypeAttr($query, $value)
    {
        if ($value) {
            $query->where('type', $value);
        }
    }

    /**
     * 状态搜索器.
     */
    public function scopeStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }
}
