<?php

declare(strict_types=1);


namespace App\Http\Dao\Category;

use App\Http\Model\Category\Category;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

class CategoryDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 分级排序列表.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getTierList($where, array $field = ['*']): array
    {
        return $this->search($where)->orderBy('sort', 'desc')->get($field)->toArray();
    }

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Category::class;
    }
}
