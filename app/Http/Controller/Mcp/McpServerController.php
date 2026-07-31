<?php

declare(strict_types=1);


namespace App\Http\Controller\Mcp;

use App\Mcp\ToolExecutor;
use App\Mcp\ToolRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MCP 服务端控制器
 *
 * 处理 MCP 协议的 JSON-RPC 请求
 */
class McpServerController
{
    /**
     * MCP 请求上下文.
     */
    protected McpRequest $mcpRequest;

    /**
     * 工具执行器.
     */
    protected ToolExecutor $toolExecutor;

    /**
     * 当前 MCP 模块端点，null 表示旧版全量入口.
     */
    protected ?string $module = null;

    public function __construct(McpRequest $mcpRequest, ToolExecutor $toolExecutor)
    {
        $this->mcpRequest = $mcpRequest;
        $this->toolExecutor = $toolExecutor;
    }

    /**
     * 处理 MCP 请求 - JSON-RPC 2.0.
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $this->module = $this->normalizeModule($request->route('module'));
            $body = $request->all();

            if (isset($body[0])) {
                return $this->handleBatch($body);
            }

            return $this->handleSingle($body);
        } catch (\Throwable $e) {
            Log::channel((string) config('mcp.logging.channel', 'mcp'))->error('MCP request error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(null, -32603, 'Internal error: ' . $e->getMessage());
        }
    }

    /**
     * 处理批量请求.
     */
    protected function handleBatch(array $requests): JsonResponse
    {
        $results = [];
        foreach ($requests as $req) {
            $results[] = $this->handleSingle($req);
        }

        return response()->json($results);
    }

    /**
     * 处理单个请求.
     */
    protected function handleSingle(array $request): JsonResponse
    {
        $id = $request['id'] ?? null;
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];

        if (($request['jsonrpc'] ?? '') !== '2.0') {
            return $this->errorResponse($id, -32600, 'Invalid JSON-RPC version');
        }

        return match ($method) {
            'initialize' => $this->handleInitialize($id, $params),
            'tools/list' => $this->handleToolsList($id),
            'tools/call' => $this->handleToolsCall($id, $params),
            'resources/list' => $this->handleResourcesList($id),
            'resources/templates/list' => $this->handleResourcesTemplatesList($id),
            'prompts/list' => $this->handlePromptsList($id),
            'ping' => $this->handlePing($id),
            default => $this->errorResponse($id, -32601, "Method not found: {$method}"),
        };
    }

    /**
     * 处理 initialize 请求.
     */
    protected function handleInitialize(mixed $id, array $params): JsonResponse
    {
        $serverInfo = config('mcp.server', []);
        $moduleInfo = ToolRegistry::getModuleConfig($this->module);
        $serverName = $serverInfo['name'] ?? 'tuoluojiang-mcp';
        if ($this->module && $moduleInfo) {
            $serverName .= '-' . $this->module;
        }

        return $this->successResponse($id, [
            'protocolVersion' => '2024-11-05',
            'capabilities' => [
                'tools' => new \stdClass(),
                'resources' => new \stdClass(),
                'prompts' => new \stdClass(),
            ],
            'serverInfo' => [
                'name' => $serverName,
                'version' => $serverInfo['version'] ?? '2.0.0',
            ],
            'instructions' => $moduleInfo['description'] ?? ($serverInfo['description'] ?? ''),
        ]);
    }

    /**
     * 处理 tools/list 请求.
     */
    protected function handleToolsList(mixed $id): JsonResponse
    {
        return $this->successResponse($id, [
            'tools' => ToolRegistry::getToolsMeta($this->module),
        ]);
    }

    /**
     * 处理 tools/call 请求.
     */
    protected function handleToolsCall(mixed $id, array $params): JsonResponse
    {
        $toolName = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];

        if (! $toolName) {
            return $this->errorResponse($id, -32602, 'Tool name is required');
        }

        $tool = ToolRegistry::getTool($toolName, $this->module);
        if (! $tool) {
            if ($this->module && ToolRegistry::getTool($toolName)) {
                return $this->errorResponse($id, -32602, "Tool not available in module {$this->module}: {$toolName}");
            }

            return $this->errorResponse($id, -32602, "Tool not found: {$toolName}");
        }

        try {
            $result = $this->toolExecutor->executeWithResult($toolName, $arguments);

            return $this->successResponse($id, $result);
        } catch (\Throwable $e) {
            Log::channel((string) config('mcp.logging.channel', 'mcp'))->error('MCP tool execution error', [
                'tool' => $toolName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->successResponse($id, [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode([
                            'error' => true,
                            'message' => $e->getMessage(),
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ],
                'isError' => true,
            ]);
        }
    }

    /**
     * 处理 resources/list 请求.
     */
    protected function handleResourcesList(mixed $id): JsonResponse
    {
        return $this->successResponse($id, ['resources' => []]);
    }

    /**
     * 处理 resources/templates/list 请求.
     */
    protected function handleResourcesTemplatesList(mixed $id): JsonResponse
    {
        return $this->successResponse($id, ['resourceTemplates' => []]);
    }

    /**
     * 处理 prompts/list 请求.
     */
    protected function handlePromptsList(mixed $id): JsonResponse
    {
        return $this->successResponse($id, ['prompts' => []]);
    }

    /**
     * 处理 ping 请求.
     */
    protected function handlePing(mixed $id): JsonResponse
    {
        return $this->successResponse($id, []);
    }

    /**
     * 返回成功响应.
     */
    protected function successResponse(mixed $id, mixed $result): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ]);
    }

    /**
     * 返回错误响应.
     */
    protected function errorResponse(mixed $id, int $code, string $message): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ]);
    }

    /**
     * 规范化模块参数.
     */
    protected function normalizeModule(mixed $module): ?string
    {
        $module = trim((string) $module);
        if ($module === '') {
            return null;
        }

        return ToolRegistry::getModuleConfig($module) ? $module : null;
    }

    /**
     * 获取 MCP 请求上下文.
     */
    public function getMcpRequest(): McpRequest
    {
        return $this->mcpRequest;
    }

    /**
     * 获取当前用户信息.
     */
    protected function getCurrentUser(): array
    {
        return $this->mcpRequest->getUserInfo();
    }

    /**
     * 获取当前用户ID.
     */
    protected function getCurrentUserId(): int
    {
        return $this->mcpRequest->getUserId();
    }

    /**
     * 检查是否为管理员.
     */
    protected function isAdmin(): bool
    {
        return $this->mcpRequest->isAdmin();
    }
}
