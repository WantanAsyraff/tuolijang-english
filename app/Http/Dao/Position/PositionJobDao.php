<?php

declare(strict_types=1);


namespace App\Http\Dao\Position;

use App\Http\Model\Position\Job;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\PathUpdateTrait;

/**
 * 企业岗位
 * Class EnterpriseJobDao.
 */
class PositionJobDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;
    use PathUpdateTrait;

    /**
     * 企业岗位.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Job::class;
    }
}
