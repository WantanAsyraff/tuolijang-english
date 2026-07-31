<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkClientFollow extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'client_id'   => 'integer',
        'createtime'  => 'integer',
        'add_way'     => 'integer',
        'is_del_user' => 'integer',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_client_follow';

    public function client(): HasOne
    {
        return $this->hasOne(WorkClient::class, 'id', 'client_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(WorkClientFollowTags::class, 'follow_id', 'id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(WorkMember::class, 'userid', 'userid');
    }

    public function scopeCreateTime($query, $value)
    {
        [$start, $end] = $value;
        if ($start > $end) {
            $query->whereBetween('createtime', [datetime_timestamp($end), datetime_timestamp($start . ' 23:59:59')]);
        } else {
            $query->whereBetween('createtime', [datetime_timestamp($start), datetime_timestamp($end . ' 23:59:59')]);
        }
    }

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }
}
