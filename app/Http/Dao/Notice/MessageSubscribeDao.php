<?php

declare(strict_types=1);


namespace App\Http\Dao\Notice;

use App\Http\Model\Message\MessageSubscribe;
use crmeb\basic\BaseDao;

class MessageSubscribeDao extends BaseDao
{
    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return MessageSubscribe::class;
    }
}
