<?php

declare(strict_types=1);


namespace App\Http\Dao\Program;

use App\Http\Model\Program\ProgramVersion;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 项目版本
 * Class ProgramVersionDao.
 */
class ProgramVersionDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ProgramVersion::class;
    }
}
