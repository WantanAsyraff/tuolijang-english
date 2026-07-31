<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Query\Builder;

/**
 * 企业日志.
 */
class CompanyLog extends BaseModel
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
     * @return mixed
     */
    public function scopeUserName($query, $value)
    {
        if ($value) {
            return $query->where('user_name', 'like', '%' . $value . '%');
        }
    }
}
