<?php

declare(strict_types=1);


namespace App\Http\Dao\Report;

use App\Http\Model\Company\DailyReportMember;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 日报汇报人
 * Class MemberDao.
 */
class MemberDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return DailyReportMember::class;
    }
}
