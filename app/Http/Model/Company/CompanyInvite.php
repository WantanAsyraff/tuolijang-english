<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use Illuminate\Database\Query\Builder;

/**
 * 企业邀请.
 */
class CompanyInvite extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'user_enterprise_invite';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'frame_id'   => 'integer',
        'is_verify'  => 'integer',
        'fail_time'  => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * uniqued作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeStatus($query, $value)
    {
        if ($value !== '') {
            return $query->where('uniqued', $value);
        }
    }

    /**
     * status作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            return $query->where('entid', $value);
        }
    }

    /**
     * frame_id作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeFrameId($query, $value)
    {
        if ($value !== '') {
            return $query->where('frame_id', $value);
        }
    }
}
