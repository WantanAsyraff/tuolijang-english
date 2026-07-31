<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------.
 */

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudApproveRecord;
use crmeb\basic\BaseDao;

/**
 * 低代码审批数据记录
 */
class SystemCrudApproveRecordDao extends BaseDao
{

    /**
     * @return string
     */
    protected function setModel()
    {
        return SystemCrudApproveRecord::class;
    }
}
