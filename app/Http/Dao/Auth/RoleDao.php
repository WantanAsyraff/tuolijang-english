<?php

declare(strict_types=1);


namespace App\Http\Dao\Auth;

use App\Http\Model\Auth\Role;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 企业角色
 * Class RoleDao.
 * @method bool insert(array $data) 插入数据
 */
class RoleDao extends BaseDao
{
    use BatchSearchTrait;
    use TogetherSearchTrait;

    /**
     * 获取管理员身份列表.
     * @param array|string[] $field
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getRoleList(array $where, array $field = ['*'], array $with = [])
    {
        return $this->search($where)->select($field)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->orderByDesc('id')->get()->toArray();
    }

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Role::class;
    }
}
