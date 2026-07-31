<?php

declare(strict_types=1);


namespace App\Http\Service\Attendance;

use App\Http\Contract\Attendance\ClockRecordInterface;
use App\Http\Dao\Attendance\ClockRecordDao;
use crmeb\basic\BaseService;
use crmeb\traits\service\ResourceServiceTrait;

/**
 * 打卡记录
 * Class ClockRecordService.
 */
class ClockRecordService extends BaseService implements ClockRecordInterface
{
    use ResourceServiceTrait;

    public function __construct(ClockRecordDao $dao)
    {
        $this->dao = $dao;
    }
}
