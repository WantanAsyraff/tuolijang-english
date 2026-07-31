<?php

declare(strict_types=1);


namespace App\Http\Service\System;

use App\Http\Dao\Category\CategoryDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 分类
 * Class CategoryService.
 */
class CategoryService extends BaseService
{
    /**
     * CategoryService constructor.
     */
    public function __construct(CategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 分级排序列表.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getTierList(array $where, array $field = ['*']): array
    {
        return get_tree_children($this->dao->getTierList($where, $field), 'children', 'value');
    }
}
