<?php

declare(strict_types=1);


namespace App\Http\Dao\Program;

use App\Http\Model\Program\ProgramTaskMember;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 项目任务成员
 * Class ProgramTaskMemberDao.
 */
class ProgramTaskMemberDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ProgramTaskMember::class;
    }
}
