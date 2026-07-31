<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\Remind;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 提醒.
 */
class RemindDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return Remind::class;
    }
}
