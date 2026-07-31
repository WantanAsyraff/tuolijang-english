<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\City;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 城市数据
 * Class CityDao.
 */
class CityDao extends BaseDao
{
    use BatchSearchTrait;

    /**
     * 获取省市区.
     * @param array|string[] $field
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getCityList(array $where = [], array $field = ['*'])
    {
        return $this->search($where)->select($field)->get()->toArray();
    }

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return City::class;
    }
}
