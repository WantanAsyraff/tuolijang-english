<?php

namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudComment;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 评论
 */
class SystemCrudCommentDao extends BaseDao
{

    use ListSearchTrait;

    protected function setModel()
    {
        return SystemCrudComment::class;
    }
}
