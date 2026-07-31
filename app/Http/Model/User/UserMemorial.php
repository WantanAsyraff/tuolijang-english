<?php

declare(strict_types=1);


namespace App\Http\Model\User;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * 笔记
 * Class UserMemorial.
 */
class UserMemorial extends BaseModel
{
    use TimeDataTrait;

    /**
     * 自动写入时间.
     * @var bool
     */
    public $timestamps = false;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'user_memorial';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'pid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 一对一关联企业用户.
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(Admin::class, 'uid', 'uid');
    }

    /**
     * Content获取器.
     * @param mixed $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * Content修改器.
     * @param mixed $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = htmlspecialchars($value);
    }

    /**
     * 一对一关联财务流水类型.
     * @return HasOne
     */
    public function cate()
    {
        return $this->hasOne(UserMemorialCategory::class, 'id', 'cate_id');
    }

    /**
     * types作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopeTitle($query, $value)
    {
        if ($value !== '') {
            return $query->where('title', 'LIKE', '%' . $value . '%')->orWhere('content', 'LIKE', '%' . $value . '%');
        }
    }

    /**
     * types作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopeCateId($query, $value)
    {
        if ($value !== '') {
            return $query->where('cate_id', $value);
        }
    }

    /**
     * pid作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopePid($query, $value)
    {
        if ($value !== '') {
            return $query->where('pid', $value);
        }
    }

    /**
     * pid作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopeUid($query, $value)
    {
        if ($value !== '') {
            return $query->where('uid', $value);
        }
    }

    /**
     * updated_at 作用域
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeUpdatedAt($query, $value)
    {
        if ($value !== '') {
            $query->where(DB::raw("DATE_FORMAT(updated_at,'%Y-%m')"), $value);
        }
    }

    /**
     * 一对一关联文件夹.
     * @return HasOne
     */
    public function parent()
    {
        return $this->hasOne(UserMemorialCategory::class, 'id', 'pid');
    }
}
