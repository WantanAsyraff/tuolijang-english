<?php

declare(strict_types=1);


namespace App\Constants;

use MyCLabs\Enum\Enum;

/**
 * 考勤组.
 */
final class AttendanceGroupEnum extends Enum
{
    /**
     * 考勤人员.
     */
    public const MEMBER = 0;

    /**
     * 考勤过滤人员.
     */
    public const FILTER = 1;

    /**
     * 考勤负责人.
     */
    public const ADMIN = 2;

    /**
     * 考勤部门.
     */
    public const FRAME = 3;

    /**
     * 补卡类型
     * 1、缺卡;2、迟到;3、严重迟到;4、早退；5、地点异常；.
     */
    public const CARD_REPLACEMENT_TYPE = [1, 2, 3, 4, 5];

    /**
     * 白名单人员.
     */
    public const WHITELIST_MEMBER = 0;

    /**
     * 白名单管理员.
     */
    public const WHITELIST_ADMIN = 1;
}
