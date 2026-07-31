<?php

declare(strict_types=1);


namespace App\Http\Model\Attendance;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 打卡记录.
 */
class ClockRecord extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'clock_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';
}
