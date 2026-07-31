<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use App\Observers\WorkClientObserver;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkClient extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'type'       => 'integer',
        'gender'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_client';

    public function scopeExternalUserid($query, $value)
    {
        is_array($value) ? $query->whereIn('external_userid', $value) : $query->where('external_userid', $value);
    }

    public function scopeUserid($query, $value)
    {
        is_array($value) ? $query->whereIn('userid', $value) : $query->where('userid', $value);
    }

    public function followOne(): HasOne
    {
        return $this->hasOne(WorkClientFollow::class, 'client_id', 'id')->orderBy('createtime');
    }

    public static function boot()
    {
        parent::boot();
        static::observe(WorkClientObserver::class);
    }
}
