<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use App\Jobs\WorkExternalContact\WorkMediaInstantUploadJob;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 素材.
 */
class WorkMedia extends BaseModel
{
    use SoftDeletes;
    use TimeDataTrait;

    protected $table = 'work_media';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'          => 'integer',
        'uid'         => 'integer',
        'up_type'     => 'integer',
        'link_id'     => 'integer',
        'attach_fail' => 'datetime:Y-m-d H:i:s',
        'fail_time'   => 'datetime:Y-m-d H:i:s',
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at'  => 'datetime:Y-m-d H:i:s',
        'deleted_at'  => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 文件地址.
     * @param mixed $value
     * @return string
     */
    public function getFileUrlAttribute($value)
    {
        return $value ? link_file($value) : '';
    }

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }

    /**
     * 正常素材.
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeNormal($query, $value)
    {
        $failTime = now()->addHour();
        $query->where('link_id', '>', 0)->where(fn ($q) => $q->where('fail_time', '<', $failTime)->orWhereNull('fail_time')->orWhereNull('media_id'));
    }
    public function scopeNotId($query, $value)
    {
        is_array($value) ? $query->whereNotIn('id', $value) : $query->whereNot('id', $value);
    }

    public function scopeJobId($query, $value)
    {
        is_bool($value) ? $query->where('job_id', '<>', '') : $query->where('job_id', $value);
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($data) {
            if ($data->link_id && (! $data->media_id || Carbon::make($data->fail_time)->subHour()->isBefore(now()))) {
                WorkMediaInstantUploadJob::dispatch($data);
            }
        });
        static::saved(function ($data) {
            if ($data->link_id && (! $data->media_id || Carbon::make($data->fail_time)->subHour()->isBefore(now()))) {
                WorkMediaInstantUploadJob::dispatch($data);
            }
        });
    }
}
