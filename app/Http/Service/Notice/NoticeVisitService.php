<?php

declare(strict_types=1);


namespace App\Http\Service\Notice;

use App\Http\Dao\Notice\NoticeVisitDao;
use App\Http\Service\Frame\FrameService;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 通知阅读记录
 * Class NoticeVisitService.
 */
class NoticeVisitService extends BaseService
{
    /**
     * NoticeVisitService constructor.
     */
    public function __construct(NoticeVisitDao $dao)
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
        $userId = app()->get(FrameService::class)->uuidToUid($uuid, $entid);
        $save   = ['user_id' => $userId, 'notice_id' => $notice_id];
        return $this->dao->firstOrCreate($save, $save);
    }
}
