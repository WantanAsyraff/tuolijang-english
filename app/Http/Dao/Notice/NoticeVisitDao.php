<?php

declare(strict_types=1);


namespace App\Http\Dao\Notice;

use App\Http\Model\News\NewsVisit;
use crmeb\basic\BaseDao;

class NoticeVisitDao extends BaseDao
{
    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return NewsVisit::class;
    }
}
