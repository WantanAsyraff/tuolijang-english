<?php

declare(strict_types=1);


namespace App\Http\Model\Cloud;

use crmeb\basic\BaseModel;

class CloudViewHistory extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'folder_view_history';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'folder_id'  => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
