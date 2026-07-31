<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 工具枚举工厂
 * 统一管理所有模块的工具枚举
 */
class McpEnumFactory
{
    /**
     * 所有模块枚举类映射
     */
    public const ENUM_CLASSES = [
        'personnel'   => McpPersonnelEnum::class,
        'customer'    => McpCustomerEnum::class,
        'lead'       => McpLeadEnum::class,
        'order'      => McpOrderEnum::class,
        'opportunity' => McpOpportunityEnum::class,
        'contract'    => McpContractEnum::class,
        'invoice'    => McpInvoiceEnum::class,
        'record'     => McpRecordEnum::class,
        'contact'    => McpContactEnum::class,
        'bill'       => McpBillEnum::class,
        'assess'     => McpAssessEnum::class,
        'report'     => McpReportEnum::class,
        'finance'    => McpFinanceEnum::class,
        'attendance' => McpAttendanceEnum::class,
        'schedule'   => McpScheduleEnum::class,
    ];

    /**
     * 获取指定模块的枚举类
     */
    public static function getEnumClass(string $moduleKey): ?string
    {
        return self::ENUM_CLASSES[$moduleKey] ?? null;
    }

    /**
     * 获取指定模块的所有工具
     */
    public static function getModuleTools(string $moduleKey): array
    {
        $enumClass = self::getEnumClass($moduleKey);
        return $enumClass ? $enumClass::getAllTools() : [];
    }

    /**
     * 获取所有模块的所有工具
     */
    public static function getAllTools(): array
    {
        $allTools = [];
        foreach (self::ENUM_CLASSES as $moduleKey => $enumClass) {
            $allTools[$moduleKey] = $enumClass::getAllTools();
        }
        return $allTools;
    }

    /**
     * 获取所有模块的工具名称映射
     */
    public static function getAllToolNames(): array
    {
        $allNames = [];
        foreach (self::ENUM_CLASSES as $moduleKey => $enumClass) {
            $allNames = array_merge($allNames, $enumClass::TOOL_NAMES);
        }
        return $allNames;
    }

    /**
     * 检查工具是否存在
     */
    public static function hasTool(string $tool): bool
    {
        foreach (self::ENUM_CLASSES as $enumClass) {
            if ($enumClass::hasTool($tool)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取工具所属模块
     */
    public static function getToolModule(string $tool): ?string
    {
        foreach (self::ENUM_CLASSES as $moduleKey => $enumClass) {
            if ($enumClass::hasTool($tool)) {
                return $moduleKey;
            }
        }
        return null;
    }

    /**
     * 获取所有模块信息
     */
    public static function getModulesInfo(): array
    {
        $modules = [];
        foreach (self::ENUM_CLASSES as $moduleKey => $enumClass) {
            $modules[$moduleKey] = [
                'name' => $enumClass::getModuleName(),
                'key' => $enumClass::getModuleKey(),
                'tools' => $enumClass::getAllTools(),
                'tool_count' => count($enumClass::getAllTools()),
            ];
        }
        return $modules;
    }
}
