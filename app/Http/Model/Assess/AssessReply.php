<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class AssessReply.
 */
class AssessReply extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'assess_reply';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'assessid'   => 'integer',
        'entid'      => 'integer',
        'user_id'    => 'integer',
        'is_own'     => 'integer',
        'types'      => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对一关联用户.
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }

    /**
     * id作用域
     */
    public function scopeId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } elseif ($value !== '') {
            $query->where('id', $value);
        }
    }

    /**
     * is_own作用域
     */
    public function scopeIsOwn($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('is_own', $value);
        } elseif ($value !== '') {
            $query->where('is_own', $value);
        }
    }
}
