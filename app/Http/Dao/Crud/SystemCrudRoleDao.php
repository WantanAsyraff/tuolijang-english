<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------.
 */

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudRole;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;

class SystemCrudRoleDao extends BaseDao
{
    use BatchSearchTrait;
    /**
     * @return string
     */
    protected function setModel()
    {
        return SystemCrudRole::class;
    }
}
