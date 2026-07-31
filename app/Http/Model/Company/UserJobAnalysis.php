<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;

/**
 * 工作分析
 * Class UserJobAnalysis.
 */
class UserJobAnalysis extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_user_job_analysis';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * data 修改器.
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = str_replace('\\', '', $value);
    }

    /**
     * data 获取器.
     */
    public function getDataAttribute($value): string
    {
        return $value ? htmlspecialchars_decode($value) : '';
    }
}
