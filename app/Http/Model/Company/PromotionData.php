<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 晋升数据.
 */
class PromotionData extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'promotion_data';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'           => 'integer',
        'promotion_id' => 'integer',
        'total'        => 'decimal:2',
        'sort'         => 'integer',
        'created_at'   => 'datetime:Y-m-d H:i:s',
        'updated_at'   => 'datetime:Y-m-d H:i:s',
        'deleted_at'   => 'datetime:Y-m-d H:i:s',
    ];

    public function setPositionAttribute($value)
    {
        $this->attributes['position'] = is_array($value) ? json_encode($value) : '';
    }

    public function getPositionAttribute($value)
    {
        return $value ? array_map('intval', json_decode($value, true)) : [];
    }

    public function setBenefitAttribute($value)
    {
        $this->attributes['benefit'] = is_array($value) ? json_encode($value) : '';
    }

    public function getBenefitAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getStandardAttribute($value)
    {
        return $value ? htmlspecialchars_decode($value) : '';
    }

    public function scopeStatus($query, $value)
    {
        if ($value !== '') {
            $query->where('status', $value);
        }
    }

    public function setRankAttribute($value)
    {
        $this->attributes['rank'] = is_array($value) ? json_encode($value) : '';
    }

    public function getRankAttribute($value)
    {
        return $value ? array_map('intval', json_decode($value, true)) : [];
    }
}
