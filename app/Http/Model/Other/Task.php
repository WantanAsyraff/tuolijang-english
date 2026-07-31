<?php

declare(strict_types=1);


namespace App\Http\Model\Other;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 任务
 * Class Task.
 */
class Task extends BaseModel
{
    use SoftDeletes;

    public const DELETED_AT = 'delete';

    /**
     * 表名.
     * @var string
     */
    protected $table = 'task';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'persist'    => 'integer',
        'run_count'  => 'integer',
        'exe_count'  => 'integer',
        'end_time'   => 'datetime:Y-m-d H:i:s',
        'rate'       => 'integer',
        'delete'     => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * interval修改器.
     */
    public function setIntervalAttribute($value)
    {
        $this->attributes['interval'] = json_encode($value);
    }

    /**
     * interval提取.
     * @return mixed
     */
    public function getIntervalAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * parameter修改器.
     */
    public function setParameterAttribute($value)
    {
        $this->attributes['parameter'] = json_encode($value);
    }

    /**
     * parameter提取.
     * @return mixed
     */
    public function getParameterAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * event提取.
     * @return array
     */
    public function getEventAttribute()
    {
        return [$this->class_name, $this->action];
    }

    /**
     * 搜索.
     */
    public function scopeUniqued($query, $value)
    {
        if ($value !== '') {
            $query->where('uniqued', $value);
        }
    }
}
