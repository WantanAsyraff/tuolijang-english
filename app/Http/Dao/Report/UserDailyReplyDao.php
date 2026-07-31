<?php

declare(strict_types=1);


namespace App\Http\Dao\Report;

use App\Http\Model\Company\UserDailyReply;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class UserDailyReplyDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserDailyReply::class;
    }
}
