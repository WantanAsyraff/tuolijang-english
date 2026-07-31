<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudEvent;
use App\Http\Model\Crud\SystemCrudEventLog;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\JoinSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 触发器日志
 * Class SystemCrudEventLogDao.
 * @email 136327134@qq.com
 * @date 2024/3/14
 */
class SystemCrudEventLogDao extends BaseDao
{
    use JoinSearchTrait;

    /**
     * 关联搜索.
     * @return mixed
     * @throws BindingResolutionException
     */
    public function joinSearch(array $where = [])
    {
        return $this->getJoinModel(joinL: 'event_id', joinR: 'id', type: 'left')
            ->when(isset($where['name']) && $where['name'] !== '', fn($q) => $q->where($this->getFiled('name', $this->aliasB), 'like', '%' . $where['name'] . '%'))
            ->when(isset($where['crud_id']) && $where['crud_id'] !== '', fn($q) => $q->where($this->getFiled('crud_id', $this->aliasB), $where['crud_id']));
    }

    /**
     * @email 136327134@qq.com
     * @date 2024/3/14
     * @return string
     */
    protected function setModel()
    {
        return SystemCrudEventLog::class;
    }

    protected function setModelB(): string
    {
        return SystemCrudEvent::class;
    }
}
