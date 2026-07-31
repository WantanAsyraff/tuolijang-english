<?php

declare(strict_types=1);


namespace App\Http\Model\Cloud;

use crmeb\basic\BaseModel;

/**
 * 云盘权限.
 */
class CloudAuth extends BaseModel
{
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'folder_auth';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'folder_id'  => 'integer',
        'create'     => 'integer',
        'read'       => 'integer',
        'update'     => 'integer',
        'download'   => 'integer',
        'delete'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeFolderId($query, $value)
    {
        is_array($value) ? $query->whereIn('folder_id', $value) : $query->where('folder_id', $value);
    }

    public function scopeUserId($query, $value)
    {
        is_array($value) ? $query->whereIn('user_id', $value) : $query->where('user_id', $value);
    }

    public function scopeNotUid($query, $value)
    {
        $query->where('uid', '<>', $value);
    }
}
