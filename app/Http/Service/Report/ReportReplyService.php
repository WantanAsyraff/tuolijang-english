<?php

declare(strict_types=1);


namespace App\Http\Service\Report;

use App\Http\Dao\Report\UserDailyReplyDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 汇报评论.
 */
class ReportReplyService extends BaseService
{
    public function __construct(UserDailyReplyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 删除回复.
     * @return null|bool|mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function deleteReply(int $id, int $dailyId, string $uid, int $entid)
    {
        $replyInfo = $this->dao->get(['id' => $id, 'daily_id' => $dailyId, 'uid' => $uid]);
        if (! $replyInfo) {
            throw $this->exception('删除失败');
        }
        return $this->dao->delete(['id' => $id]);
    }
}
