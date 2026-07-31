<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkClientFollowTagsDao;
use crmeb\basic\BaseService;

/**
 * 客户标签
 * Class WorkClientFollowTagsService.
 */
class WorkClientFollowTagsService extends BaseService
{
    public function __construct(WorkClientFollowTagsDao $dao)
    {
        $this->dao = $dao;
    }
}
