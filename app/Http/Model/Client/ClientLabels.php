<?php

declare(strict_types=1);


namespace App\Http\Model\Client;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 客户标签关联表
 * Class ClientLabels.
 */
class ClientLabels extends BaseModel
{
    /**
     * @var string
     */
    protected $id = 'id';

    /**
     * @var string
     */
    protected $table = 'client_labels';

    /**
     * 客户ID作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeEid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('eid', $value);
        } elseif ($value !== '') {
            $query->where('eid', $value);
        }
    }

    /**
     * 标签ID作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeLabelId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('label_id', $value);
        } elseif ($value !== '') {
            $query->where('label_id', $value);
        }
    }
}
