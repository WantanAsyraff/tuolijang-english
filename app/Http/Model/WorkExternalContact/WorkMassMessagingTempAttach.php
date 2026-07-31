<?php

declare(strict_types=1);


namespace App\Http\Model\WorkExternalContact;

use App\Constants\Work\MediaEnum;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 群发素材模板附件.
 */
class WorkMassMessagingTempAttach extends BaseModel
{
    use TimeDataTrait;

    protected $table = 'work_mass_messaging_temp_attach';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'temp_id'    => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function file()
    {
        return $this->hasOne(WorkMedia::class, 'link_id', 'id')->where('link_type', MediaEnum::LINK_TYPE_MASS);
    }

    public function scopeId($query, $value)
    {
        is_array($value) ? $query->whereIn('id', $value) : $query->where('id', $value);
    }
}
