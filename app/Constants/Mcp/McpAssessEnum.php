<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 绩效管理工具枚举
 */
final class McpAssessEnum extends McpBaseEnum
{
    // ========== 绩效查询 ==========

    /**
     * 绩效列表
     */
    public const ASSESS_LIST = 'assess_list';

    /**
     * 绩效详情
     */
    public const ASSESS_DETAIL = 'assess_detail';

    /**
     * 绩效统计
     */
    public const ASSESS_STATISTICS = 'assess_statistics';

    /**
     * 绩效趋势
     */
    public const ASSESS_TREND = 'assess_trend';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::ASSESS_LIST => '绩效列表',
        self::ASSESS_DETAIL => '绩效详情',
        self::ASSESS_STATISTICS => '绩效统计',
        self::ASSESS_TREND => '绩效趋势',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '绩效管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'assess';
    }
}
