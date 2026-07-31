<?php

declare(strict_types=1);


namespace App\Http\Model\Crud;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemCrudSeniorSearch extends BaseModel
{
    use SoftDeletes;

    /**
     * 表明.
     * @var string
     */
    protected $table = 'system_crud_senior_search';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'             => 'integer',
        'crud_id'        => 'integer',
        'user_id'        => 'integer',
        'sort'           => 'integer',
        'senior_type'    => 'integer',
        'search_boolean' => 'integer',
        'created_at'     => 'datetime:Y-m-d H:i:s',
        'updated_at'     => 'datetime:Y-m-d H:i:s',
        'deleted_at'     => 'datetime:Y-m-d H:i:s',
    ];

    public function getSeniorSearchAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @email 136327134@qq.com
     * @date 2024/3/19
     */
    public function setSeniorSearchAttribute($value)
    {
        $this->attributes['senior_search'] = json_encode($value);
    }
}
