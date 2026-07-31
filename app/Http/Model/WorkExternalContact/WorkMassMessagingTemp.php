<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 企微群发消息模板.
 */
class WorkMassMessagingTemp extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    protected $table = 'work_mass_messaging_temp';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'group_id'   => 'integer',
        'types'      => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 关联创建人.
     * @return HasOne
     */
    public function creator()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }

    /**
     * 关联附件.
     * @return HasMany
     */
    public function attach()
    {
        return $this->hasMany(WorkMassMessagingTempAttach::class, 'temp_id', 'id')->with(['file']);
    }

    /**
     * 关联分组查询.
     * @return HasOne
     */
    public function group()
    {
        return $this->hasOne(WorkMassMessagingTempGroup::class, 'id', 'group_id')->select(['id', 'name']);
    }

    public function scopeNameLike($query, $value)
    {
        $query->where('content', 'like', '%' . $value . '%');
    }
}
