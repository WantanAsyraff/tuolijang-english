<?php

declare(strict_types=1);


namespace App\Http\Dao\Chat;

use App\Http\Model\Chat\ChatHistory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ChatHistoryDao.
 */
class ChatHistoryDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function topUpModel(int $userId)
    {
        return $this->getModel()->where('user_id', $userId)->whereNotNull('top_up');
    }

    /**
     * @return string
     */
    protected function setModel()
    {
        return ChatHistory::class;
    }
}
