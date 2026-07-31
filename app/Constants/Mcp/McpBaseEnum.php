<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 工具枚举基类
 */
abstract class McpBaseEnum
{
    /**
     * 工具名称映射 [常量名 => 中文名称]
     */
    public const TOOL_NAMES = [];

    /**
     * 获取所有工具名称
     */
    public static function getAllTools(): array
    {
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();

        return array_filter($constants, function ($value) {
            return is_string($value) && ! empty($value) && $value !== 'TOOL_NAMES';
        });
    }

    /**
     * 获取工具名称映射
     */
    public static function getToolNames(): array
    {
        return static::TOOL_NAMES;
    }

    /**
     * 检查工具是否存在
     */
    public static function hasTool(string $tool): bool
    {
        return in_array($tool, static::getAllTools(), true);
    }

    /**
     * 获取工具中文名称
     */
    public static function getToolName(string $tool): string
    {
        return static::TOOL_NAMES[$tool] ?? $tool;
    }

    /**
     * 获取模块名称
     */
    abstract public static function getModuleName(): string;

    /**
     * 获取模块标识
     */
    abstract public static function getModuleKey(): string;
}
