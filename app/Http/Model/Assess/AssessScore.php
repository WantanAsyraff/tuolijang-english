<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use crmeb\basic\BaseModel;

/**
 * Class AssessScore.
 */
class AssessScore extends BaseModel
{
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'assess_score';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'      => 'integer',
        'entid'   => 'integer',
        'user_id' => 'integer',
        'min'     => 'integer',
        'max'     => 'integer',
        'level'   => 'integer',
    ];

    public function scopeScore($query, $value)
    {
        if ($value !== '') {
            $query->where('min', '<', $value)->where('max', '>=', $value);
        }
    }

    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            $query->where('entid', $value);
        }
    }

    public function scopeLevel($query, $value)
    {
        if ($value !== '') {
            $query->where('level', $value);
        }
    }
}
