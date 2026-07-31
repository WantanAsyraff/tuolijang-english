<?php

declare(strict_types=1);


namespace App\Http\Dao\Program;

use App\Http\Model\Program\ProgramTaskComment;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 项目任务评论
 * Class ProgramTaskCommentDao.
 */
class ProgramTaskCommentDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ProgramTaskComment::class;
    }
}
