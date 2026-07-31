<?php

declare(strict_types=1);


namespace App\Http\Model\Schedule;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 日程表.
 */
class ScheduleTask extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'schedule_task';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'pid'        => 'integer',
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time'   => 'datetime:Y-m-d H:i:s',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
