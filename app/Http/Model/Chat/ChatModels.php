<?php

declare(strict_types=1);


namespace App\Http\Model\Chat;

use App\Http\Model\Admin\Admin;
use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ClientBill.
 */
class ChatModels extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'uid'        => 'integer',
        'provider'   => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @var string
     */
    protected $table = 'chat_models';

    public function getJsonAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setJsonAttribute($value)
    {
        $this->attributes['json'] = json_encode($value);
    }

    public function applications()
    {
        return $this->hasMany(ChatApplications::class, 'models_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }

    public function scopeNameLike($query, $value)
    {
        $query->where('name', 'like', "%{$value}%");
    }
}
