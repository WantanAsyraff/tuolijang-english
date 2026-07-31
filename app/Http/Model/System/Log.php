<?php

declare(strict_types=1);


namespace App\Http\Model\System;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Query\Builder;

/**
 * Class Log.
 */
class Log extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_log';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 用户名作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopeUserName($query, $value)
    {
        if ($value) {
            return $query->where('user_name', 'like', '%' . $value . '%');
        }
    }
}
