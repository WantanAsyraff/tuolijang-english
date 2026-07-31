<?php

declare(strict_types=1);


use App\Http\Controller\Mcp\McpServerController;
use App\Http\Middleware\McpAuthMiddleware;
use App\Http\Service\System\ModulePermissionService;
use App\Mcp\ToolRegistry;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MCP 路由配置
|--------------------------------------------------------------------------
|
| MCP (Model Context Protocol) 服务路由
| 使用用户 McpKey 进行认证
|
*/

// MCP 服务端点 - 使用 JSON-RPC 2.0 协议
Route::match(['get', 'post'], '/mcp', [McpServerController::class, 'handle'])
    ->middleware([
        McpAuthMiddleware::class,  // MCP 认证中间件（支持 mcpKey 参数/Header/Bearer）
    ])
    ->name('mcp.handle');

// MCP 模块端点 - 仅暴露指定模块工具，通用身份/权限工具会自动附加
Route::match(['get', 'post'], '/mcp/{module}', [McpServerController::class, 'handle'])
    ->middleware([
        McpAuthMiddleware::class,
    ])
    ->where('module', 'customer|attendance|assess|report|schedule')
    ->name('mcp.module.handle');

// MCP 健康检查（不需要认证）
Route::get('/mcp/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => config('mcp.server.name', 'tuoluojiang-mcp'),
        'version' => config('mcp.server.version', '2.0.0'),
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('mcp.health');

// MCP 服务信息（需要认证）
Route::get('/mcp/info', function () {
    $userDbId = (int) request()->input('mcp_user_db_id', 0);
    $isAdmin = (bool) request()->input('mcp_is_admin', false);

    // 获取用户在各模块的权限
    $userPermissions = [];
    if ($userDbId > 0 && ! $isAdmin) {
        try {
            $modulePermService = app(ModulePermissionService::class);
            $userPermissions = $modulePermService->getUserAllPermissions($userDbId);
        } catch (\Throwable $e) {
            // 获取失败时返回空
        }
    }

    return response()->json([
        'server' => config('mcp.server', []),
        'auth' => [
            'enabled' => config('mcp.auth.enabled', true),
            'type' => 'mcpKey',
        ],
        'modules' => config('mcp.modules', []),
        'user_permissions' => $userPermissions,
        'tools_count' => ToolRegistry::discover() ? count(ToolRegistry::getTools()) : 0,
    ]);
})->middleware(McpAuthMiddleware::class)->name('mcp.info');

// MCP 模块服务信息（需要认证）
Route::get('/mcp/{module}/info', function (string $module) {
    $moduleConfig = ToolRegistry::getModuleConfig($module);
    if (! $moduleConfig) {
        return response()->json(['message' => 'MCP module not found'], 404);
    }

    return response()->json([
        'server' => config('mcp.server', []),
        'module' => [
            'key' => $module,
            'name' => $moduleConfig['name'] ?? $module,
            'description' => $moduleConfig['description'] ?? '',
        ],
        'auth' => [
            'enabled' => config('mcp.auth.enabled', true),
            'type' => 'mcpKey',
        ],
        'common_tools' => ToolRegistry::getCommonToolNames(),
        'tools_count' => count(ToolRegistry::getToolsMeta($module)),
    ]);
})->middleware(McpAuthMiddleware::class)
    ->where('module', 'customer|attendance|assess|report|schedule')
    ->name('mcp.module.info');
