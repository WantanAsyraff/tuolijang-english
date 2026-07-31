<?php

declare(strict_types=1);


namespace App\Http\Dao\Chat;

use App\Http\Model\Chat\ChatAppAuth;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;

/**
 * ai模型
 * Class ChatModelsDao.
 */
class ChatAppAuthDao extends BaseDao
{
    use ListSearchTrait;
    use BatchSearchTrait;

    protected function setModel(): string
    {
        return ChatAppAuth::class;
    }
}
