<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------.
 */

declare(strict_types=1);


namespace App\Http\Model\Crud;

use App\Http\Model\System\Menus;
use crmeb\basic\BaseModel;

class SystemCrudCate extends BaseModel
{
    /**
     * 表明.
     * @var string
     */
    protected $table = 'system_crud_cate';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeIds($query, $value)
    {
        $query->whereIn('id', $value);
    }

    public function scopeName($query, $value)
    {
        if ($value)
            $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * 关联菜单
     * @return mixed
     */
    public function menu()
    {
        return $this->hasOne(Menus::class, 'crud_app_id', 'id');
    }
}
