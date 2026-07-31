<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use App\Http\Model\Customer\Label;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

class WorkClientFollowTags extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'follow_id'  => 'integer',
        'type'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'work_client_follow_tags';

    public function tag()
    {
        return $this->hasOne(Label::class, 'work_tag_id', 'tag_id');
    }
}
