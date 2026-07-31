<?php

declare(strict_types=1);


namespace App\Http\Service\Crud;

use App\Http\Dao\Crud\SystemCrudTableDao;
use crmeb\basic\BaseService;

/**
 * 实体列表默认
 * Class SystemCrudTableService.
 * @email 136327134@qq.com
 * @date 2024/4/13
 * @mixin SystemCrudTableDao
 */
class SystemCrudTableService extends BaseService
{
    /**
     * SystemCrudTableService constructor.
     */
    public function __construct(SystemCrudTableDao $dao)
    {
        $this->dao = $dao;
    }
}
