<?php

declare(strict_types=1);


namespace App\Http\Dao\News;

use App\Http\Model\News\News;
use crmeb\basic\BaseDao;

/**
 * 企业动态Dao.
 */
class NewsDao extends BaseDao
{

    protected function setModel(): string
    {
        return News::class;
    }
}
