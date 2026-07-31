<?php

declare(strict_types=1);


namespace App\Http\Dao\Category;

use App\Http\Model\Category\MessageCategory;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

class MessageCategoryDao extends BaseDao
{
    use ListSearchTrait;
    use BatchSearchTrait;

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
     */
    protected function setModel(): string
    {
        return MessageCategory::class;
    }
}
