<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;

/**
 * 调薪记录
 * Class UserSalary.
 */
class UserSalary extends BaseModel
{
    /**
     * 表名.
     * @var string
     */
    protected $table = 'enterprise_user_salary';

    /**
     * 主键.
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'card_id'    => 'integer',
        'total'      => 'decimal:2',
        'take_date'  => 'date:Y-m-d',
        'link_id'    => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value ? json_encode($value) : '';
    }

    public function getContentAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function scopeEntid($query, $value)
    {
        if ($value !== '') {
            $query->where('entid', $value);
        }
    }

    public function scopeCardId($query, $value)
    {
        if ($value !== '') {
            $query->where('card_id', $value);
        }
    }
}
