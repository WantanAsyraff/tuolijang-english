<?php

declare(strict_types=1);


namespace App\Http\Service\Crud;

use App\Http\Dao\Config\DictTypeDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class DataDictService.
 * @email 136327134@qq.com
 * @date 2024/2/29
 */
class DataDictService extends BaseService
{
    /**
     * DataDictService constructor.
     */
    public function __construct(DictTypeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     * @email 136327134@qq.com
     * @date 2024/2/29
     */
    public function getDataDicList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list           = $this->dao->getList($where, ['*'], $page, $limit, 'id');
        $count          = $this->dao->count($where);
        return $this->listData($list, $count);
    }
}
