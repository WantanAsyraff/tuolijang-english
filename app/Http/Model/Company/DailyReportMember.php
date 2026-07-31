<?php

declare(strict_types=1);


namespace App\Http\Model\Company;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * 日报汇报人
 * Class DailyReportMember.
 */
class DailyReportMember extends BaseModel
{
    use TimeDataTrait;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'daily_report_member';

    protected $casts = [
        'id'         => 'integer',
        'daily_id'   => 'integer',
        'member'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * member作用域
     */
    public function scopeMember($query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('member', $value);
        } elseif ($value !== '') {
            $query->where('member', $value);
        }
    }
}
