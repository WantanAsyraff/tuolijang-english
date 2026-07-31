<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;

/**
 * 员工培训.
 */
class EmployeeTrain extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'employee_train';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Content获取器.
     */
    public function getContentAttribute($value): string
    {
        return $value ? htmlspecialchars_decode($value) : '';
    }
}
