<?php

declare(strict_types=1);


namespace App\Http\Dao\Chat;

use App\Http\Model\Chat\ChatRecord;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class ChatRecordDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return ChatRecord::class;
    }
}
