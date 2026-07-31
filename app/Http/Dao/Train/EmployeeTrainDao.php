<?php

declare(strict_types=1);


namespace App\Http\Dao\Train;

use App\Http\Model\Company\EmployeeTrain;
use crmeb\basic\BaseDao;

/**
 * 员工培训Dao.
 * Class EmployeeTrainDao.
 */
class EmployeeTrainDao extends BaseDao
{
    protected function setModel()
    {
        return EmployeeTrain::class;
    }
}
