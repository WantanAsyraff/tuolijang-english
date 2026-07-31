<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 个人教育经历.
 */
class EducationHistory extends BaseModel
{
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'user_education_history';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'resume_id'  => 'integer',
        'start_time' => 'date:Y-m-d',
        'end_time'   => 'date:Y-m-d',
    ];

    /**
     * id作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeId($query, $value)
    {
        if (is_array($value)) {
            return $query->whereIn('id', $value);
        }
        return $query->where('id', $value);
    }

    public function scopeResumeId($query, $value)
    {
        if ($value !== '') {
            return $query->where('resume_id', $value);
        }
    }
}
