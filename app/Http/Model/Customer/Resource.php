<?php

declare(strict_types=1);


namespace App\Http\Model\Customer;

use App\Http\Model\Admin\Admin;
use App\Http\Model\Attach\SystemAttach;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 订单附件
 * Class Resource.
 */
class Resource extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'eid'        => 'integer',
        'cid'        => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'contract_resource';

    /**
     * 附件一对多关联.
     *
     * @return HasMany
     */
    public function attachs()
    {
        return $this->hasMany(SystemAttach::class, 'relation_id', 'id')
            ->where('relation_type', 3)->select(['id', 'att_dir as url', 'relation_id', 'real_name as name', 'att_size as size']);
    }

    /**
     * 名片.
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'phone']);
    }
}
