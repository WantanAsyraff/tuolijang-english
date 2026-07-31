<?php

declare(strict_types=1);


namespace App\Http\Service\News;

use App\Http\Dao\News\NewsVisitDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 阅读记录.
 */
class NewsVisitService extends BaseService
{
    public function __construct(NewsVisitDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 保存访问记录.
     * @param mixed $entid
     * @return bool
     * @throws BindingResolutionException
     */
    public function saveVisit($notice_id, $uuid, $entid)
    {
        $save = ['user_id' => uuid_to_uid($uuid, $entid), 'notice_id' => $notice_id];
        return $this->dao->firstOrCreate($save, $save);
    }
}
