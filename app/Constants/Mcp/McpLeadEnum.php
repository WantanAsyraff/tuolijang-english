<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 线索管理工具枚举
 */
final class McpLeadEnum extends McpBaseEnum
{
    // ========== 线索查询 ==========

    /**
     * 线索列表
     */
    public const LEAD_LIST = 'lead_list';

    /**
     * 线索详情
     */
    public const LEAD_DETAIL = 'lead_detail';

    /**
     * 线索统计
     */
    public const LEAD_STATISTICS = 'lead_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::LEAD_LIST => '线索列表',
        self::LEAD_DETAIL => '线索详情',
        self::LEAD_STATISTICS => '线索统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '线索管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'lead';
    }
}
