<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 客户管理工具枚举
 */
final class McpCustomerEnum extends McpBaseEnum
{
    // ========== 客户CRUD ==========

    /**
     * 列出客户
     */
    public const LIST_CUSTOMERS = 'list_customers';

    /**
     * 获取客户详情
     */
    public const GET_CUSTOMER = 'get_customer';

    /**
     * 创建客户
     */
    public const CREATE_CUSTOMER = 'create_customer';

    /**
     * 更新客户
     */
    public const UPDATE_CUSTOMER = 'update_customer';

    /**
     * 删除客户
     */
    public const DELETE_CUSTOMER = 'delete_customer';

    /**
     * 搜索客户
     */
    public const SEARCH_CUSTOMERS = 'search_customers';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::LIST_CUSTOMERS => '列出客户',
        self::GET_CUSTOMER => '获取客户详情',
        self::CREATE_CUSTOMER => '创建客户',
        self::UPDATE_CUSTOMER => '更新客户',
        self::DELETE_CUSTOMER => '删除客户',
        self::SEARCH_CUSTOMERS => '搜索客户',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '客户管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'customer';
    }
}
