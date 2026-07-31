<?php

declare(strict_types=1);


namespace App\Http\Dao\Auth;

use App\Http\Model\Auth\RoleUser;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\GroupDateSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class RoleUser.
 */
class RoleUserDao extends BaseDao
{
    use BatchSearchTrait;
    use ListSearchTrait;
    use GroupDateSearchTrait;

    /**
     * 获取角色ID.
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getRoleIds(array $where)
    {
        return $this->search($where)->select('role_id')->groupBy('role_id')->get()->map(function ($item) {
            return $item['role_id'];
        })->toArray();
    }

    public function getCount($where)
    {
        return $this->search($where)->distinct()->count('user_id');
    }

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return RoleUser::class;
    }
}
