<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use App\Http\Model\Admin\Admin;
use App\Http\Service\Admin\AdminService;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

class WorkGroupChat extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'                => 'integer',
        'group_create_time' => 'integer',
        'member_num'        => 'integer',
        'retreat_group_num' => 'integer',
        'status'            => 'integer',
        'created_at'        => 'datetime:Y-m-d H:i:s',
        'updated_at'        => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_group_chat';

    public function getGroupCreateTimeAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function scopeAdminList($query, $value)
    {
        if (is_array($value)) {
            $query->where(function ($que) use ($value) {
                foreach ($value as $v) {
                    $que->orWhereJsonContains('admin_list', $v)->orWhereJsonContains('admin_list', (string) $v);
                }
            });
        } else {
            $query->where(function ($que) use ($value) {
                $que->orWhereJsonContains('admin_list', $value)->orWhereJsonContains('admin_list', (string) $value);
            });
        }
    }

    public function scopeAdminId($query, $value)
    {
        if (is_array($value)) {
            $value = collect(app()->get(AdminService::class)->select(['id' => $value], with: ['work']))->pluck('work.userid')->filter()->unique()->values()->all();
            $query->whereIn('owner', $value);
        } else {
            $value = collect(app()->get(AdminService::class)->select(['id' => $value], with: ['work']))->pluck('work.userid')->filter()->unique()->values()->all();
            $query->where('owner', end($value));
        }
    }

    public function scopeOwner($query, $value)
    {
        is_array($value) ? $query->whereIn('owner', $value) : $query->where('owner', $value);
    }

    public function scopeNameLike($query, $value)
    {
        $query->where('name', 'like', "%{$value}%");
    }

    public function admin()
    {
        return $this->hasOneThrough(Admin::class, WorkMember::class, 'userid', 'work_member_id', 'owner', 'id')->select(['admin.id', 'admin.name', 'admin.avatar', 'admin.phone']);
    }
}
