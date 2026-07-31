<?php

declare(strict_types=1);


namespace App\Http\Dao\User;

use App\Http\Model\User\User;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\TogetherSearchTrait;

class UserDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;
    use TogetherSearchTrait;

    /**
     * @return mixed|string
     */
    protected function setModel()
    {
        return User::class;
    }
}
