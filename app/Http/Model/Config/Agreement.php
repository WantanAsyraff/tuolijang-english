<?php

declare(strict_types=1);


namespace App\Http\Model\Config;

use crmeb\basic\BaseModel;

class Agreement extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'agreement';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * data获取器.
     * @param mixed $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * data修改器.
     * @param mixed $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = htmlspecialchars($value);
    }
}
