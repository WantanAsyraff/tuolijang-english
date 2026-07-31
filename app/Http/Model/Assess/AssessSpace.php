<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AssessSpace.
 */
class AssessSpace extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'assess_space';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'assessid'   => 'integer',
        'targetid'   => 'integer',
        'ratio'      => 'integer',
        'sort'       => 'integer',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对多关联指标.
     * @return HasMany
     */
    public function target()
    {
        return $this->hasMany(AssessTarget::class, 'spaceid', 'id');
    }

    /**
     * id作用域
     */
    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }

    /**
     * id作用域
     */
    public function scopeAssessid($query, $value)
    {
        is_array($value) ? $query->whereIn('assessid', $value) : $query->where('assessid', $value);
    }

    /**
     * id作用域
     */
    public function scopeTargetid($query, $value)
    {
        is_array($value) ? $query->whereIn('targetid', $value) : $query->where('targetid', $value);
    }
}
