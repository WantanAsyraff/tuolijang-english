<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 任职经历
 * Class UserPosition.
 */
class UserPosition extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_user_position';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $casts = [
        'start_time' => 'datetime:Y-m-d',
        'end_time'   => 'datetime:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'id'         => 'integer',
        'card_id'    => 'integer',
        'is_admin'   => 'integer',
        'status'     => 'integer',
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
}
