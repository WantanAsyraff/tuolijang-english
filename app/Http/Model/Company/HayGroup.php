<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use App\Http\Model\Admin\Admin;
use App\Http\Model\Position\Job;
use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 评估表.
 */
class HayGroup extends BaseModel
{
    use SoftDeletes;

    /**
     * 表名.
     * @var string
     */
    protected $table = 'hay_group';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 远程一对多关联职位.
     * @return HasManyThrough
     */
    public function positions()
    {
        return $this->hasManyThrough(
            Job::class,
            HayGroupData::class,
            'group_id',
            'id',
            'id',
            'col1'
        )->select(['rank_job.id', 'rank_job.name']);
    }

    /**
     * 一对一远程关联用户.
     * @return HasOne
     */
    public function card()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'uid', 'name', 'avatar', 'phone']);
    }
}
