<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Model\Admin\Admin;
use App\Mcp\Context\McpUserContextResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * MCP 认证中间件
 * Class McpAuthMiddleware.
 *
 * 功能：
 * 1. 从请求参数或请求头获取 mcpKey
 * 2. 通过 mcpKey 查询员工表 admin 获取当前用户
 * 3. 获取员工信息（用户ID、角色、企业ID等）
 * 4. 将用户信息注入到请求上下文中，供后续 Tool 使用
 * 5. 如果认证失败，返回 401 Unauthorized
 */
class McpAuthMiddleware
{
    /**
     * @var null|string
     */
    private ?string $mcpKey = null;

    /**
     * @var null|Admin
     */
    private ?Admin $mcpUser = null;

    /**
     * MCP Key Header 名称.
     */
    private const HEADER_MCP_KEY = 'X-Mcp-Key';

    /**
     * MCP Key Header 兼容名称.
     */
    private const HEADER_MCP_KEY_ALIAS = 'Mcp-Key';

    /**
     * 执行中间件.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        $authMethod = 'mcp_key';

        try {
            $this->authenticateWithMcpKey($request);
        } catch (\Throwable $e) {
            $this->logger()->warning('MCP mcpKey auth failed', array_merge([
                'error' => $e->getMessage(),
            ], $this->requestLogContext($request)));

            return $this->unauthorizedResponse($e->getMessage());
        }

        // 设置用户上下文
        try {
            $this->setUserContext($request);
        } catch (\Exception $e) {
            $this->logger()->error('MCP setUserContext failed', [
                'auth_method' => $authMethod,
                'mcp_key' => $this->mcpKey ? substr($this->mcpKey, 0, 12) . '...' : null,
                'error' => $e->getMessage(),
            ]);
            return $this->unauthorizedResponse('Failed to get user context: ' . $e->getMessage());
        }

        if ($this->shouldLogAuthSuccess()) {
            $user = $request->input('mcp_user');
            $this->logger()->info('MCP Auth success', array_merge([
                'auth_method' => $authMethod,
                'user_id' => $user?->id,
                'mcp_user_id' => $request->input('mcp_user_id'),
                'user_name' => $user?->name ?? '',
            ], $this->requestLogContext($request)));
        }

        return $next($request);
    }

    /**
     * 使用 mcpKey 查询员工并完成认证.
     */
    private function authenticateWithMcpKey(Request $request): void
    {
        $mcpKey = $this->resolveMcpKey($request);
        if ($mcpKey === '') {
            throw new \RuntimeException('No mcpKey provided');
        }

        $user = Admin::where('mcp_key', $mcpKey)->where('status', 1)->first();
        if (! $user) {
            throw new \RuntimeException('Invalid mcpKey or user not found');
        }

        $this->mcpKey = $mcpKey;
        $this->mcpUser = $user;
    }

    /**
     * 获取请求中的 mcpKey，兼容参数、Header 和 Bearer 传递方式.
     */
    private function resolveMcpKey(Request $request): string
    {
        $candidates = [
            $request->input('mcpKey'),
            $request->input('mcp_key'),
            $request->input('params.mcpKey'),
            $request->input('params.mcp_key'),
            $request->header(self::HEADER_MCP_KEY),
            $request->header(self::HEADER_MCP_KEY_ALIAS),
        ];

        foreach ($candidates as $candidate) {
            $mcpKey = trim((string) $candidate);
            if ($mcpKey !== '') {
                return $mcpKey;
            }
        }

        $bearerToken = trim((string) $request->bearerToken());
        if ($bearerToken !== '' && ! str_contains($bearerToken, '.')) {
            return $bearerToken;
        }

        return '';
    }

    /**
     * 设置用户上下文.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function setUserContext(Request $request): void
    {
        $user = $this->mcpUser;

        if ($this->shouldLogUserContext()) {
            $this->logger()->debug('MCP setUserContext', array_merge([
                'has_user' => $user !== null,
                'user_id' => $user?->id,  // 使用数据库ID
                'user_db_id' => $user?->id,
                'user_name' => $user?->name ?? '',
            ], $this->requestLogContext($request)));
        }

        if (! $user) {
            throw new \RuntimeException('User not found');
        }

        app(McpUserContextResolver::class)->merge($request, $user);
    }

    /**
     * 返回 401 未授权响应.
     */
    private function unauthorizedResponse(string $message = 'Unauthorized'): mixed
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'error' => [
                'code' => -32600,
                'message' => $message,
            ],
        ], 401);
    }

    /**
     * 正常认证成功日志量较大，默认关闭，排查问题时可通过 MCP_LOG_AUTH_SUCCESS=true 开启。
     */
    private function shouldLogAuthSuccess(): bool
    {
        return (bool) config('mcp.logging.log_success', false);
    }

    /**
     * 用户上下文日志包含用户信息，默认关闭，排查上下文问题时可开启。
     */
    private function shouldLogUserContext(): bool
    {
        return (bool) config('mcp.logging.log_user_context', false);
    }

    /**
     * MCP 异常认证日志附带来源信息，便于定位误轮询或失效 token 的客户端。
     */
    private function requestLogContext(Request $request): array
    {
        $mcpKey = $this->mcpKey ?: $this->resolveMcpKey($request);

        return [
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'mcp_key_prefix' => $mcpKey ? substr($mcpKey, 0, 12) . '...' : null,
        ];
    }

    /**
     * 获取 MCP 专用日志通道，确保 MCP 日志写入 storage/logs/mcp 目录。
     */
    private function logger(): LoggerInterface
    {
        return Log::channel((string) config('mcp.logging.channel', 'mcp'));
    }
}
