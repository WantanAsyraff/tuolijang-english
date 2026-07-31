<?php

declare(strict_types=1);


namespace App\Http\Model\Assess;

use crmeb\basic\BaseModel;

/**
 * Class TemplateCollect.
 */
class TemplateCollect extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_template_collect';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'user_id'    => 'integer',
        'temp_id'    => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return mixed
     */
    public function scopeUserId($query, $val)
    {
        if ($val !== '') {
            return $query->where('user_id', $val);
        }
    }

    /**
     * @return mixed
     */
    public function scopeTempId($query, $val)
    {
        if ($val !== '') {
            return $query->where('temp_id', $val);
        }
    }
}
