<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkLabelDao;
use crmeb\basic\BaseService;

/**
 * 标签
 * Class WorkLabelService.
 */
class WorkLabelService extends BaseService
{
    public function __construct(WorkLabelDao $dao)
    {
        $this->dao = $dao;
    }
}
