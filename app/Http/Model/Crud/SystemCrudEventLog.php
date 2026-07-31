<?php

declare(strict_types=1);


namespace App\Http\Model\Crud;

use crmeb\basic\BaseModel;

/**
 * Class SystemCrudEventLog.
 * @email 136327134@qq.com
 * @date 2024/3/14
 */
class SystemCrudEventLog extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_crud_event_log';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'crud_id'    => 'integer',
        'event_id'   => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @email 136327134@qq.com
     * @date 2024/2/24
     * @param mixed $value
     */
    public function setParameterAttribute($value)
    {
        $this->attributes['parameter'] = json_encode($value);
    }

    /**
     * @email 136327134@qq.com
     * @date 2024/2/24
     * @param mixed $value
     * @return mixed
     */
    public function getParameterAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @email 136327134@qq.com
     * @date 2024/2/24
     * @param mixed $value
     */
    public function setLogAttribute($value)
    {
        $this->attributes['log'] = json_encode($value);
    }

    /**
     * @email 136327134@qq.com
     * @date 2024/2/24
     * @param mixed $value
     * @return mixed
     */
    public function getLogAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getActionAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function crud()
    {
        return $this->hasOne(SystemCrud::class, 'id', 'crud_id');
    }
}
