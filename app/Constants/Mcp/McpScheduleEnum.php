<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 日程管理工具枚举
 */
final class McpScheduleEnum extends McpBaseEnum
{
    // ========== 日程查询 ==========

    /**
     * 日程列表
     */
    public const SCHEDULE_LIST = 'schedule_list';

    /**
     * 日程详情
     */
    public const SCHEDULE_DETAIL = 'schedule_detail';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::SCHEDULE_LIST => '日程列表',
        self::SCHEDULE_DETAIL => '日程详情',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '日程管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'schedule';
    }
}
