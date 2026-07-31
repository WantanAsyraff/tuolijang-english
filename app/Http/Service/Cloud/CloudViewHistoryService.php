<?php

declare(strict_types=1);


namespace App\Http\Service\Cloud;

use App\Http\Dao\Cloud\CloudViewHistoryDao;
use crmeb\basic\BaseService;

/**
 * 文件浏览历史.
 * @mixin CloudViewHistoryDao
 */
class CloudViewHistoryService extends BaseService
{
    public function __construct(CloudViewHistoryDao $dao)
    {
        $this->dao = $dao;
    }
}
