<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;

/**
 * 省市区
 * Class SystemBackup.
 */
class Backup extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_backup';

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

    public function scopeVersion($query, $value)
    {
        if ($value !== '') {
            $query->where('version', $value);
        }
    }
}
