<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 订单管理工具枚举
 */
final class McpOrderEnum extends McpBaseEnum
{
    // ========== 订单查询 ==========

    /**
     * 订单列表
     */
    public const ORDER_LIST = 'order_list';

    /**
     * 订单详情
     */
    public const ORDER_DETAIL = 'order_detail';

    /**
     * 订单统计
     */
    public const ORDER_STATISTICS = 'order_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::ORDER_LIST => '订单列表',
        self::ORDER_DETAIL => '订单详情',
        self::ORDER_STATISTICS => '订单统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '订单管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'order';
    }
}
