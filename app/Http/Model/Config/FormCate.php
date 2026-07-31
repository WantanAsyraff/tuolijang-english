<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 自定义表单分组.
 */
class FormCate extends BaseModel
{
    use SoftDeletes;

    protected $table = 'form_cate';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'sort'       => 'integer',
        'types'      => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function data()
    {
        return $this->hasMany(FormData::class, 'cate_id', 'id')->orderByDesc('sort');
    }
}
