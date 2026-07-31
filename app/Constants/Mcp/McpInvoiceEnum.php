<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 发票管理工具枚举
 */
final class McpInvoiceEnum extends McpBaseEnum
{
    // ========== 发票查询 ==========

    /**
     * 发票列表
     */
    public const INVOICE_LIST = 'invoice_list';

    /**
     * 发票详情
     */
    public const INVOICE_DETAIL = 'invoice_detail';

    /**
     * 发票统计
     */
    public const INVOICE_STATISTICS = 'invoice_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::INVOICE_LIST => '发票列表',
        self::INVOICE_DETAIL => '发票详情',
        self::INVOICE_STATISTICS => '发票统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '发票管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'invoice';
    }
}
