<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 财务管理工具枚举
 */
final class McpFinanceEnum extends McpBaseEnum
{
    // ========== 财务查询 ==========

    /**
     * 财务流水列表
     */
    public const FINANCE_LIST = 'finance_list';

    /**
     * 财务流水详情
     */
    public const FINANCE_DETAIL = 'finance_detail';

    /**
     * 资金趋势分析
     */
    public const FINANCE_TREND = 'finance_trend';

    /**
     * 资金占比分析
     */
    public const FINANCE_RANK_ANALYSIS = 'finance_rank_analysis';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::FINANCE_LIST => '财务流水列表',
        self::FINANCE_DETAIL => '财务流水详情',
        self::FINANCE_TREND => '资金趋势分析',
        self::FINANCE_RANK_ANALYSIS => '资金占比分析',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '财务管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'finance';
    }
}
