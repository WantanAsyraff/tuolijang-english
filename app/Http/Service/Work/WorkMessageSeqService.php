<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkMessageSeqDao;
use crmeb\basic\BaseService;

/**
 * 群聊消息序列号
 * Class WorkMessageSeqService.
 */
class WorkMessageSeqService extends BaseService
{
    public function __construct(WorkMessageSeqDao $dao)
    {
        $this->dao = $dao;
    }
}
