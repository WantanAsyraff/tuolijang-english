<?php

declare(strict_types=1);


namespace App\Http\Model\System;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 菜单.
 */
class Quick extends BaseModel
{
    protected $appends = ['is_system_owned'];

    public function getIsSystemOwnedAttribute(): int
    {
        $systemNames = [
            '填写汇报', '发起申请', '记事本', '通讯录', '客户列表', '合同管理', '发票管理',
            '收支记账', '收支统计', '业绩统计', '速记', '工作汇报', '考勤打卡', '公司介绍',
            '云盘', '订单收支', '合同收支', '项目管理', '我的任务', '新建日程',
        ];
        return in_array((string) $this->getAttribute('name'), $systemNames, true) ? 1 : 0;
    }
    /**
     * 表名.
     * @var string
     */
    protected $table = 'system_quick';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'cid'        => 'integer',
        'sort'       => 'integer',
        'types'      => 'integer',
        'pc_show'    => 'integer',
        'uni_show'   => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 关联查询分类.
     * @return HasOne
     */
    public function cate()
    {
        return $this->hasOne(Category::class, 'id', 'cid');
    }

    public function scopeNotId($query, $value)
    {
        if ($value !== '') {
            $query->where('id', '<>', $value);
        }
    }

    public function scopeCid($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('cid', $value);
        } elseif ($value !== '') {
            $query->where('cid', $value);
        }
    }

    public function scopeNameLike($query, $value)
    {
        if ($value !== '') {
            $query->where('name', 'LIKE', "%{$value}%");
        }
    }
}
