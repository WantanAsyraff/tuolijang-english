<?php

declare(strict_types=1);


namespace App\Http\Service\Message;

use App\Http\Dao\Message\MessageTemplateDao;
use crmeb\basic\BaseService;

/**
 * 消息模板
 * Class MessageTemplateService.
 * @method insert(array $data) 批量新增
 */
class MessageTemplateService extends BaseService
{
    /**
     * MessageTemplateService constructor.
     */
    public function __construct(MessageTemplateDao $dao)
    {
        $this->dao = $dao;
    }
}
