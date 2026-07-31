<?php

declare(strict_types=1);


namespace App\Http\Model\Schedule;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 日程提醒记录表.
 */
class ScheduleRecord extends BaseModel
{
    use TimeDataTrait;

    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'schedule_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'schedule_id' => 'integer',
        'status'      => 'integer',
        'remind_day'  => 'date:Y-m-d',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];
}
