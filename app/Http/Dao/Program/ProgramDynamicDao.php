<?php

declare(strict_types=1);


namespace App\Http\Dao\Program;

use App\Http\Model\Program\ProgramDynamic;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 项目动态
 * Class ProgramDynamic.
 */
class ProgramDynamicDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return ProgramDynamic::class;
    }
}
