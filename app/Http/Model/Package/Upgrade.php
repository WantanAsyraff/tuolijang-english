<?php

declare(strict_types=1);


namespace App\Http\Model\Package;

use crmeb\basic\BaseModel;

class Upgrade extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'upgrade';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';
}
