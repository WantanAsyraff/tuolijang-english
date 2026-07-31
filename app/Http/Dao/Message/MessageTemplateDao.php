<?php

declare(strict_types=1);


namespace App\Http\Dao\Message;

use App\Http\Model\Message\MessageTemplate;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

/**
 * Class MessageTemplateDao.
 */
class MessageTemplateDao extends BaseDao
{
    use BatchSearchTrait;

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return MessageTemplate::class;
    }
}
