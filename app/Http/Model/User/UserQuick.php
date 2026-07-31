<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use crmeb\basic\BaseModel;

/**
 * 用户快捷入口.
 */
class UserQuick extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'user_quick';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getPcMenuIdAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getAppMenuIdAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setStatisticsTypeAttribute($value)
    {
        $this->attributes['statistics_manage'] = is_array($value) ? json_encode($value) : '';
    }

    public function getStatisticsTypeAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}
