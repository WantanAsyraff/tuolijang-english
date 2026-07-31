<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudDashboard;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class SystemCrudDashboardDao.
 */
class SystemCrudDashboardDao extends BaseDao
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return SystemCrudDashboard::class;
    }
}
