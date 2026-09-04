<?php

declare(strict_types=1);


namespace App\Http\Model\Category;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * 消息分类
 * Class MessageCategory.
 */
class MessageCategory extends BaseModel
{
    protected $appends = ['is_system_owned'];

    public function getIsSystemOwnedAttribute(): int
    {
        $systemCategories = [
            76 => '日常汇报',
            77 => '绩效考核',
            78 => '申请审批',
            79 => '客户合同',
            80 => '财务审核',
            81 => '待办日程',
            82 => '人员异动',
            83 => '企业注册',
            84 => '企业文件',
            85 => '个人办公',
            95 => '企业动态',
        ];
        $id = (int) $this->getAttribute('id');
        return isset($systemCategories[$id])
            && $systemCategories[$id] === (string) $this->getAttribute('cate_name') ? 1 : 0;
    }
    /**
     * 表名.
     * @var string
     */
    protected $table = 'message_category';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'pid'        => 'integer',
        'sort'       => 'integer',
        'is_show'    => 'integer',
        'uni_show'   => 'integer',
        'level'      => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * path修改器.
     */
    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value ? implode('/', $value) : '';
    }

    /**
     * path获取器.
     * @return false|string[]
     */
    public function getPathAttribute($value)
    {
        return $value ? array_map('intval', explode('/', $value)) : [];
    }

    /**
     * 关键词作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsShow($query, $value)
    {
        return $value !== '' ? $query->where('is_show', $value) : null;
    }

    /**
     * 分类名称作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeCateName($query, $value)
    {
        return $value !== '' ? $query->where('cate_name', 'LIKE', "%{$value}%") : null;
    }

    /**
     * 分类名称作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeEqCateName($query, $value)
    {
        $query->where('cate_name', $value);
    }

    /**
     * 屏蔽ID作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotId($query, $value)
    {
        return $value ? $query->where('id', '<>', $value) : null;
    }

    /**
     * 屏蔽ID作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopePid($query, $value)
    {
        return $value ? $query->where('pid', $value) : null;
    }

    /**
     * 屏蔽ID作用域
     * @param Builder $query
     * @return Builder
     */
    public function scopeLtLevel($query, $value)
    {
        if ($value !== '') {
            $query->where('level', 'LT', $value);
        }
    }
}
