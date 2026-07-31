<?php

declare(strict_types=1);


namespace App\Http\Dao\Chat;

use App\Http\Model\Chat\ChatModels;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * ai模型
 * Class ChatModelsDao.
 */
class ChatModelsDao extends BaseDao
{
    use ListSearchTrait;

    public function search($where, ?bool $authWhere = null)
    {
        if (isset($where['name'])) {
            $name = $where['name'];
            unset($where['name']);
        }
        if (isset($where['uids'])) {
            $uids = $where['uids'];
            unset($where['uids']);
        }
        return parent::search($where, $authWhere)
            ->when(isset($name) && $name !== '', fn ($q) => $q->where('name', 'like', '%' . $name . '%'))
            ->when(isset($uids) && $uids !== '', fn ($q) => $q->whereIn('uid', $uids));
    }

    protected function setModel(): string
    {
        return ChatModels::class;
    }
}
