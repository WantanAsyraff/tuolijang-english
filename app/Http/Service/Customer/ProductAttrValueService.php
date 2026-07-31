<?php

declare(strict_types=1);


namespace App\Http\Service\Customer;

use App\Http\Dao\Client\CustomerProductAttrValueDao;
use App\Http\Dao\Customer\ProductAttrValueDao;
use crmeb\basic\BaseService;

/**
 * 产品属性值service
 * @mixin ProductAttrValueDao
 */
class ProductAttrValueService extends BaseService
{
    public function __construct(ProductAttrValueDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取关联列表.
     * @param null|mixed $sort
     */
    public function getList(array $where, array $field = ['*'], $sort = null, array $with = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list           = $this->dao->joinSearch($where, $page, $limit, $with, $sort)->get()?->toArray();
        $count          = $this->dao->joinSearch($where)->count();
        return $this->listData($list, $count);
    }
}
