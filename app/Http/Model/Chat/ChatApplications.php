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
class ChatApplications extends BaseModel
{
    use TimeDataTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'           => 'integer',
        'uid'          => 'integer',
        'status'       => 'integer',
        'use_limit'    => 'integer',
        'sort'         => 'integer',
        'models_id'    => 'integer',
        'count_number' => 'integer',
        'created_at'   => 'datetime:Y-m-d H:i:s',
        'updated_at'   => 'datetime:Y-m-d H:i:s',
        'deleted_at'   => 'datetime:Y-m-d H:i:s',
        'is_table'     => 'integer',
        'source_type'  => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'chat_applications';

    /**
     * 修改权限.
     * @param mixed $value
     * @return mixed
     */
    public function getEditAttribute($value)
    {
        if ($value) {
            return explode('/', trim($value, '/'));
        }
        return [];
    }

    /**
     * 修改权限.
     * @param mixed $value
     * @return mixed
     */
    public function setEditAttribute($value)
    {
        $this->attributes['edit'] = '/' . implode('/', $value) . '/';
    }

    public function getAuthIdsAttribute($value)
    {
        if ($value) {
            return $value ? json_decode($value, true) : [];
        }
        return [];
    }

    public function setAuthIdsAttribute($value)
    {
        $this->attributes['auth_ids'] = json_encode($value);
    }

    public function getMcpJsonAttribute($value)
    {
        if ($value) {
            return $value ? json_decode($value, true) : [];
        }
        return [];
    }

    public function setMcpJsonAttribute($value)
    {
        $this->attributes['mcp_json'] = json_encode($value);
    }

    public function getJsonAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setJsonAttribute($value)
    {
        $this->attributes['json'] = json_encode($value);
    }

    public function getKeywordAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setKeywordAttribute($value)
    {
        $this->attributes['keyword'] = json_encode($value);
    }

    public function getTablesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setTablesAttribute($value)
    {
        $this->attributes['tables'] = json_encode($value);
    }

    public function getPrologueListAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setPrologueListAttribute($value)
    {
        $this->attributes['prologue_list'] = json_encode($value);
    }

    public function getPrologueTextAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setPrologueTextAttribute($value)
    {
        $this->attributes['prologue_text'] = json_encode($value);
    }

    public function auth()
    {
        return $this->hasMany(ChatAppAuth::class, 'app_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(Admin::class, 'id', 'uid')->select(['id', 'name', 'avatar', 'uid', 'phone']);
    }
}
