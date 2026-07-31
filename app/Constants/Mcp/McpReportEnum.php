<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 汇报管理工具枚举
 */
final class McpReportEnum extends McpBaseEnum
{
    // ========== 汇报查询 ==========

    /**
     * 汇报列表
     */
    public const REPORT_LIST = 'report_list';

    /**
     * 汇报详情
     */
    public const REPORT_DETAIL = 'report_detail';

    /**
     * 汇报统计
     */
    public const REPORT_STATISTICS = 'report_statistics';

    /**
     * 汇报提交统计
     */
    public const REPORT_SUBMIT_STATISTICS = 'report_submit_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::REPORT_LIST => '汇报列表',
        self::REPORT_DETAIL => '汇报详情',
        self::REPORT_STATISTICS => '汇报统计',
        self::REPORT_SUBMIT_STATISTICS => '汇报提交统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '汇报管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'report';
    }
}
