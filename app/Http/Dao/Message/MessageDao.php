<?php

declare(strict_types=1);


namespace App\Http\Dao\Message;

use App\Http\Model\Message\Message;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class MessageDao.
 */
class MessageDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return Message::class;
    }
}
