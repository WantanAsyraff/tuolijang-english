<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 商机管理工具枚举
 */
final class McpOpportunityEnum extends McpBaseEnum
{
    // ========== 商机查询 ==========

    /**
     * 商机列表
     */
    public const OPPORTUNITY_LIST = 'opportunity_list';

    /**
     * 商机详情
     */
    public const OPPORTUNITY_DETAIL = 'opportunity_detail';

    /**
     * 商机统计
     */
    public const OPPORTUNITY_STATISTICS = 'opportunity_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::OPPORTUNITY_LIST => '商机列表',
        self::OPPORTUNITY_DETAIL => '商机详情',
        self::OPPORTUNITY_STATISTICS => '商机统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '商机管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'opportunity';
    }
}
