<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use App\Http\Model\Admin\Admin;
use App\Http\Model\Frame\Frame;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Query\Builder;

/**
 * 辅助表
 * Class Assist.
 */
class Assist extends BaseModel
{
    /**
     * 关闭自动写入时间.
     * @var bool
     */
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'assist';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'main_id'    => 'integer',
        'aux_id'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * main_id作用域
     * @param Builder $query
     * @return mixed
     */
    public function scopeMainId($query, $value)
    {
        if (is_array($value)) {
            return $query->whereIn('main_id', $value);
        }
        if ($value !== '') {
            return $query->where('main_id', $value);
        }
    }

    /**
     * 获取关联用户.
     * @return HasOne
     */
    public function users()
    {
        return $this->hasOne(Admin::class, 'aux_id', 'uid');
    }

    /**
     * 远程一对一
     * @return HasOneThrough
     */
    public function frame()
    {
        return $this->hasOneThrough(
            Admin::class,
            Frame::class,
            'id',
            'uid',
            'main_id',
            'id'
        );
    }

    /**
     * @return HasOne
     */
    public function hasFrame()
    {
        return $this->hasOne(Frame::class, 'id', 'aux_id');
    }

    public function scopeFrameIds($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('frame_id', $value);
        }
    }
}
