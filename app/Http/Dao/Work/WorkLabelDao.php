<?php

declare(strict_types=1);


namespace App\Http\Dao\Work;

use App\Http\Model\Work\WorkLabel;
use crmeb\basic\BaseDao;

class WorkLabelDao extends BaseDao
{
    protected function setModel()
    {
        return WorkLabel::class;
    }
}
