<?php

declare(strict_types=1);


namespace App\Http\Dao\Program;

use App\Http\Model\Program\ProgramMember;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 项目成员
 * Class ProgramMemberDao.
 */
class ProgramMemberDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ProgramMember::class;
    }
}
