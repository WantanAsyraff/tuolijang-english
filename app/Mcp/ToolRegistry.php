<?php
declare(strict_types=1);

namespace App\Mcp;

use App\Mcp\Tools\Contracts\ToolInterface;
use Illuminate\Support\Facades\Cache;

class ToolRegistry
{
    private static ?array $tools = null;

    private static array $toolDirs = [];

    /**
     * 自动发现所有工具
     */
    public static function discover(): array
    {
        if (self::$tools !== null) {
            return self::$tools;
        }

        $tools = [];
        self::$toolDirs = [];
        $basePath = app_path('Mcp/Tools');

        // 扫描所有模块目录
        $modules = self::scanModules($basePath);

        foreach ($modules as $module => $toolClasses) {
            foreach ($toolClasses as $toolClass) {
                if (is_subclass_of($toolClass, ToolInterface::class)) {
                    $tool = self::instantiateTool($toolClass);
                    if ($tool) {
                        $toolName = $tool->getName();
                        $tools[$toolName] = $tool;
                        self::$toolDirs[$toolName] = $module;
                    }
                }
            }
        }

        self::$tools = $tools;
        return $tools;
    }

    /**
     * 扫描模块目录获取所有工具类
     */
    protected static function scanModules(string $basePath): array
    {
        $modules = [];

        if (!is_dir($basePath)) {
            return $modules;
        }

        $dirs = array_filter(scandir($basePath), fn($d) => $d !== '.' && $d !== '..' && is_dir($basePath . '/' . $d));

        foreach ($dirs as $module) {
            $modulePath = $basePath . '/' . $module;
            $toolClasses = self::findToolClasses($modulePath, $module);
            if (!empty($toolClasses)) {
                $modules[$module] = $toolClasses;
            }
        }

        return $modules;
    }

    /**
     * 在模块目录中查找工具类
     */
    protected static function findToolClasses(string $modulePath, string $moduleNamespace): array
    {
        $classes = [];
        $files = glob($modulePath . '/*Tool.php');

        foreach ($files as $file) {
            $className = self::getClassNameFromFile($file, $moduleNamespace);
            if ($className) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * 从文件获取类名
     */
    protected static function getClassNameFromFile(string $file, string $moduleNamespace): ?string
    {
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+([^;]+)/', $content, $matches)) {
            $namespace = trim($matches[1]);
            if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
                return $namespace . '\\' . $classMatches[1];
            }
        }
        return null;
    }

    /**
     * 实例化工具
     */
    protected static function instantiateTool(string $className): ?ToolInterface
    {
        try {
            $mcpRequest = app(\App\Http\Controller\Mcp\McpRequest::class);
            return new $className($mcpRequest);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * 获取所有工具列表
     */
    public static function getTools(): array
    {
        return self::discover();
    }

    /**
     * 获取模块工具列表.
     */
    public static function getModuleTools(?string $module = null, bool $includeCommon = true): array
    {
        $tools = self::discover();
        if ($module === null || $module === '') {
            return $tools;
        }

        $allowedNames = self::getModuleToolNames($module, $includeCommon);
        if ($allowedNames === []) {
            return [];
        }

        return array_intersect_key($tools, array_flip($allowedNames));
    }

    /**
     * 获取指定工具
     */
    public static function getTool(string $name, ?string $module = null): ?ToolInterface
    {
        $tools = self::getModuleTools($module);
        return $tools[$name] ?? null;
    }

    /**
     * 获取工具元数据列表 (用于 tools/list)
     */
    public static function getToolsMeta(?string $module = null, bool $includeCommon = true): array
    {
        $tools = self::getModuleTools($module, $includeCommon);
        $result = [];

        foreach ($tools as $tool) {
            $result[] = [
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'inputSchema' => $tool->getInputSchema(),
            ];
        }

        return $result;
    }

    /**
     * 获取工具所属 MCP 业务模块.
     */
    public static function getToolModule(string $toolName): ?string
    {
        self::discover();

        $dir = self::$toolDirs[$toolName] ?? null;
        if (! $dir) {
            return null;
        }

        foreach (self::getConfiguredModules() as $module => $config) {
            if (in_array($dir, (array) ($config['tool_dirs'] ?? []), true)) {
                return $module;
            }
        }

        if (in_array($toolName, self::getCommonToolNames(), true)) {
            return 'common';
        }

        return null;
    }

    /**
     * 判断工具是否允许在指定模块端点中暴露或调用.
     */
    public static function isToolAvailableInModule(string $toolName, ?string $module, bool $includeCommon = true): bool
    {
        if ($module === null || $module === '') {
            return isset(self::discover()[$toolName]);
        }

        return in_array($toolName, self::getModuleToolNames($module, $includeCommon), true);
    }

    /**
     * 获取配置的 MCP 模块.
     */
    public static function getConfiguredModules(): array
    {
        $modules = config('mcp.tool_modules', []);
        return is_array($modules) ? $modules : [];
    }

    /**
     * 获取模块配置.
     */
    public static function getModuleConfig(?string $module): array
    {
        if ($module === null || $module === '') {
            return [];
        }

        $modules = self::getConfiguredModules();
        return $modules[$module] ?? [];
    }

    /**
     * 获取模块允许的工具名称.
     */
    public static function getModuleToolNames(string $module, bool $includeCommon = true): array
    {
        self::discover();

        $config = self::getModuleConfig($module);
        if ($config === []) {
            return [];
        }

        $toolDirs = (array) ($config['tool_dirs'] ?? []);
        $names = [];
        foreach (self::$toolDirs as $toolName => $dir) {
            if (in_array($dir, $toolDirs, true)) {
                $names[] = $toolName;
            }
        }

        if ($includeCommon) {
            $names = array_merge($names, self::getCommonToolNames());
        }

        return array_values(array_unique(array_filter($names, fn ($name) => isset(self::$tools[$name]))));
    }

    /**
     * 获取通用上下文工具名称.
     */
    public static function getCommonToolNames(): array
    {
        $tools = config('mcp.common_tools', []);
        return is_array($tools) ? array_values(array_filter(array_map('strval', $tools))) : [];
    }

    /**
     * 清除缓存的工具列表
     */
    public static function clearCache(): void
    {
        self::$tools = null;
        self::$toolDirs = [];
    }
}
