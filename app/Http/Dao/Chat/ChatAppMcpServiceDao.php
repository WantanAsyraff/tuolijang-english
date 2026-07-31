<?php

declare(strict_types=1);


namespace App\Http\Dao\Chat;

use App\Http\Model\Chat\ChatAppMcpService;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class ChatAppMcpServiceDao.
 */
class ChatAppMcpServiceDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ChatAppMcpService::class;
    }
}
