<?php

declare(strict_types=1);


namespace App\Http\Model\Cloud;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;

/**
 * 云盘文件分享.
 */
class CloudShare extends BaseModel
{
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'folder_share';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'folder_id'  => 'integer',
        'auth_id'    => 'integer',
        'entid'      => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id');
    }

    public function auth()
    {
        return $this->hasOne(CloudAuth::class, 'id', 'auth_id');
    }
}
