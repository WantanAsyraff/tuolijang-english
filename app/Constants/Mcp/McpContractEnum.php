<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 合同管理工具枚举
 */
final class McpContractEnum extends McpBaseEnum
{
    // ========== 合同查询 ==========

    /**
     * 合同列表
     */
    public const CONTRACT_LIST = 'contract_list';

    /**
     * 合同详情
     */
    public const CONTRACT_DETAIL = 'contract_detail';

    /**
     * 合同统计
     */
    public const CONTRACT_STATISTICS = 'contract_statistics';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::CONTRACT_LIST => '合同列表',
        self::CONTRACT_DETAIL => '合同详情',
        self::CONTRACT_STATISTICS => '合同统计',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '合同管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'contract';
    }
}
