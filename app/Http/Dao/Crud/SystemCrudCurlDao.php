<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudCurl;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * curl接口管理.
 */
class SystemCrudCurlDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel()
    {
        return SystemCrudCurl::class;
    }
}
