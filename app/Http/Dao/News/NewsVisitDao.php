<?php

declare(strict_types=1);


namespace App\Http\Dao\News;

use App\Http\Model\News\NewsVisit;
use crmeb\basic\BaseDao;

class NewsVisitDao extends BaseDao
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
