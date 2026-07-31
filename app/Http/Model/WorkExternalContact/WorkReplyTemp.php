<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use App\Constants\Work\MediaEnum;
use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 快捷回复模板
 */
class WorkReplyTemp extends BaseModel
{
    use TimeDataTrait;

    protected $table = 'work_reply_temp';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'uid'         => 'integer',
        'group_id'    => 'integer',
        'sort'        => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
        'deleted_at'  => 'datetime:Y-m-d H:i:s',
        'is_personal' => 'integer',
    ];

    /**
     * 是否个人库 - 否
     */
    public const IS_PERSONAL_NO = 0;

    /**
     * 是否个人库 - 是
     */
    public const IS_PERSONAL_YES = 1;


    /**
     * Content获取器.
     * @param mixed $value
     */
    public function getContentAttribute($value): string
    {
        return $value ? htmlspecialchars_decode($value) : '';
    }

    /**
     * 关联文件查询.
     * @return HasOne
     */
    public function file()
    {
        return $this->hasOne(WorkMedia::class, 'link_id', 'id')->where('link_type', MediaEnum::LINK_TYPE_REPLY);
    }

    /**
     * 关联创建人查询.
     * @return HasOne
     */
    public function creator()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }

    /**
     * 关联分组查询.
     * @return HasOne
     */
    public function group()
    {
        return $this->hasOne(WorkReplyTempGroup::class, 'id', 'group_id')->select(['id', 'name']);
    }

    public function scopeNameLike($query, $value)
    {
        $query->where(function ($q) use ($value) {
            $q->where('content', 'like', '%' . $value . '%')
                ->orWhere('title', 'like', '%' . $value . '%')
                ->orWhereExists(function ($subquery) use ($value) {
                    $subquery->selectRaw(1)
                        ->from('work_media')
                        ->whereColumn('work_media.link_id', $this->table . '.id')
                        ->where('work_media.link_type', 'work_reply_temp')
                        ->where('work_media.real_name', 'like', "%{$value}%");
                });
        });
    }
}
