<?php

declare(strict_types=1);


namespace App\Http\Model\Package;

use crmeb\basic\BaseModel;

class UpgradeRecord extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'upgrade_record';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';
}
