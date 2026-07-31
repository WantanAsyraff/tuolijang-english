<?php

declare(strict_types=1);


namespace App\Constants\ProgramEnum;

use MyCLabs\Enum\Enum;

/**
 * 项目管理：项目状态
 */
final class ProgramStatusEnum extends Enum
{
    /**
     * 项目状态：正常.
     */
    public const NORMAL = 0;

    /**
     * 项目状态：暂停.
     */
    public const SUSPEND = 1;

    /**
     * 项目状态：关闭.
     */
    public const CLOSE = 2;

    public static function getStatusText(int $status): string
    {
        return match ($status) {
            0       => '正常',
            1       => '暂停',
            2       => '关闭',
            default => '未知',
        };
    }
}
