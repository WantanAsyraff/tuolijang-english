<?php

declare(strict_types=1);


namespace App\Http\Model\Position;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 职级等级
 * Class Level.
 */
class Level extends BaseModel
{
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'rank_level';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'        => 'integer',
        'entid'     => 'integer',
        'min_level' => 'integer',
        'max_level' => 'integer',
    ];

    /**
     * id作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeId($query, $value)
    {
        if ($value) {
            return $query->where('id', $value);
        }
    }

    /**
     * entid作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            return $query->where('entid', $value);
        }
    }
}
