<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 跟进记录工具枚举
 */
final class McpRecordEnum extends McpBaseEnum
{
    // ========== 跟进记录查询 ==========

    /**
     * 跟进记录列表
     */
    public const RECORD_LIST = 'record_list';

    /**
     * 跟进记录详情
     */
    public const RECORD_DETAIL = 'record_detail';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::RECORD_LIST => '跟进记录列表',
        self::RECORD_DETAIL => '跟进记录详情',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '跟进记录';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'record';
    }
}
