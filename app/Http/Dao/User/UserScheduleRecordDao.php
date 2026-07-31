<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserScheduleRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserScheduleRecordDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    public function setModel()
    {
        return UserScheduleRecord::class;
    }
}
