<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 账目管理工具枚举
 */
final class McpBillEnum extends McpBaseEnum
{
    // ========== 账目查询 ==========

    /**
     * 账目列表
     */
    public const BILL_LIST = 'bill_list';

    /**
     * 账目详情
     */
    public const BILL_DETAIL = 'bill_detail';

    /**
     * 账目统计
     */
    public const BILL_STATISTICS = 'bill_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::BILL_LIST => '账目列表',
        self::BILL_DETAIL => '账目详情',
        self::BILL_STATISTICS => '账目统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '账目管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'bill';
    }
}
