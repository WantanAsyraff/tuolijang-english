<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\System\Quick;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use crmeb\traits\dao\PathUpdateTrait;

class QuickDao extends BaseDao
{
    use ListSearchTrait;
    use PathUpdateTrait;

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return Quick::class;
    }
}
