<?php

declare(strict_types=1);


namespace App\Constants;

use MyCLabs\Enum\Enum;

/**
 * 汇报.
 */
final class DailyEnum extends Enum
{
    /**
     * 汇报状态：删除.
     */
    public const DAILY_DELETE = -1;

    /**
     * 汇报状态：未创建.
     */
    public const DAILY_NOT_SUB = 0;

    /**
     * 汇报状态：已创建.
     */
    public const DAILY_SUB = 1;

    /**
     * 数据关联通知.
     */
    public const LINK_NOTICE = [
        NoticeEnum::DAILY_SHOW_REMIND_TYPE,
        NoticeEnum::DAILY_UPDATE_REMIND_TYPE,
    ];

    /**
     * 无数据关联通知.
     */
    public const Not_Link_Notice = [
        NoticeEnum::DAILY_REMIND_TYPE,
    ];

    /**
     * 汇报类型.
     */
    public static function getDailyTypeName(int $types): string
    {
        return match ($types) {
            1       => '周报',
            2       => '月报',
            3       => '汇报',
            default => '日报',
        };
    }
}
