<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use App\Http\Model\Company\UserPosition;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class UserPositionDao.
 */
class UserPositionDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return UserPosition::class;
    }
}
