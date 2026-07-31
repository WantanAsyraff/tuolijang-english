<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 联系人管理工具枚举
 */
final class McpContactEnum extends McpBaseEnum
{
    // ========== 联系人查询 ==========

    /**
     * 联系人列表
     */
    public const CONTACT_LIST = 'contact_list';

    /**
     * 联系人详情
     */
    public const CONTACT_DETAIL = 'contact_detail';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::CONTACT_LIST => '联系人列表',
        self::CONTACT_DETAIL => '联系人详情',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '联系人管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'contact';
    }
}
