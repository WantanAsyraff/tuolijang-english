<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\UserEnterpriseApply;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class UserEnterpriseApplyDao.
 */
class UserEnterpriseApplyDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 插入数据.
     * @return bool
     * @throws BindingResolutionException
     */
    public function insert(array $data)
    {
        return $this->getModel(false)->insert($data);
    }

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserEnterpriseApply::class;
    }
}
