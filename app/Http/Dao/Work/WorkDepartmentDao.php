<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkDepartment;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class WorkDepartmentDao extends BaseDao
{
    use BatchSearchTrait;

    protected function setModel()
    {
        return WorkDepartment::class;
    }
}
