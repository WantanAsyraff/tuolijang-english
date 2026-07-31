<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\GroupData;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class GroupDataDao.
 */
class GroupDataDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    /**
     * 获取组合数据.
     * @param array|string[] $field
     * @param int $limit
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getGroupDataList(array $where, array $field = ['*'], int $page = 0, $limit = 0)
    {
        return $this->search($where)->select($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->forPage($page, $limit);
        })->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        })->orderByDesc('sort')->get()->toArray();
    }

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return GroupData::class;
    }
}
