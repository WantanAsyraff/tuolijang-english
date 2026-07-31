<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkMessageIndexDao;
use crmeb\basic\BaseService;

/**
 * Class WorkMessageIndexService.
 */
class WorkMessageIndexService extends BaseService
{
    public function __construct(WorkMessageIndexDao $dao)
    {
        $this->dao = $dao;
    }
}
