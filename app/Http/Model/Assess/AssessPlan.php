<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * Class AssessPlan.
 */
class AssessPlan extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'assess_plan';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $casts = [
        'test_frame'   => 'array',
        'test_user'    => 'array',
        'id'           => 'integer',
        'entid'        => 'integer',
        'create_time'  => 'integer',
        'create_month' => 'integer',
        'assess_type'  => 'integer',
        'period'       => 'integer',
        'make_day'     => 'integer',
        'eval_day'     => 'integer',
        'verify_day'   => 'integer',
        'status'       => 'integer',
        'created_at'   => 'datetime:Y-m-d H:i:s',
        'updated_at'   => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }
}
