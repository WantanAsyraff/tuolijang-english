<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudTableUser;
use crmeb\basic\BaseDao;

/**
 * Class SystemCrudTableUserDao.
 * @email 136327134@qq.com
 * @date 2024/3/9
 */
class SystemCrudTableUserDao extends BaseDao
{
    /**
     * @return string
     * @email 136327134@qq.com
     * @date 2024/3/9
     */
    protected function setModel()
    {
        return SystemCrudTableUser::class;
    }
}
