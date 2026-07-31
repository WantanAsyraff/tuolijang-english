<?php

declare(strict_types=1);


namespace App\Mcp;

use crmeb\services\HttpService;
use Illuminate\Support\Facades\Log;

/**
 * 外部 MCP 客户端
 * 通过 JSON-RPC 2.0 over HTTP 连接外部 MCP 服务器.
 */
class ExternalMcpClient
{
    private string $serviceUrl;

    private array $headers;

    private int $timeout;

    private int $requestId = 1;

    private bool $initialized = false;

    private ?array $serverInfo = null;

    public function __construct(string $serviceUrl, array $headers = [], int $timeout = 30)
    {
        $this->serviceUrl = rtrim($serviceUrl, '/');
        $this->headers = $headers;
        $this->timeout = $timeout;
    }

    /**
     * 发送 JSON-RPC 2.0 请求.
     */
    private function sendRequest(string $method, array $params = []): array
    {
        $id = $this->requestId++;
        $body = [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'method'  => $method,
            'params'  => $params,
        ];

        $headerList = ['Content-Type: application/json'];
        foreach ($this->headers as $key => $value) {
            $headerList[] = "{$key}: {$value}";
        }

        try {
            [$httpCode, $content] = (new HttpService())
                ->setHeader($headerList)
                ->requests($this->serviceUrl, 'POST', json_encode($body), true, $this->timeout);

            if (! $httpCode || $httpCode < 200 || $httpCode >= 300) {
                Log::channel((string) config('mcp.logging.channel', 'mcp'))->error('External MCP request failed', [
                    'url'     => $this->serviceUrl,
                    'method'  => $method,
                    'httpCode' => $httpCode,
                    'content' => $content,
                ]);
                return ['error' => true, 'message' => "HTTP {$httpCode}: {$content}"];
            }

            $response = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => true, 'message' => 'JSON parse error: ' . json_last_error_msg()];
            }

            if (isset($response['error'])) {
                return [
                    'error'   => true,
                    'message' => $response['error']['message'] ?? 'Unknown error',
                    'code'    => $response['error']['code'] ?? -1,
                ];
            }

            return $response['result'] ?? [];
        } catch (\Throwable $e) {
            Log::channel((string) config('mcp.logging.channel', 'mcp'))->error('External MCP connection error', [
                'url'   => $this->serviceUrl,
                'error' => $e->getMessage(),
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * 发送 initialize 请求，获取服务能力信息.
     */
    public function initialize(): array
    {
        $result = $this->sendRequest('initialize', [
            'protocolVersion' => '2024-11-05',
            'capabilities'    => new \stdClass(),
            'clientInfo'      => [
                'name'    => 'tuoluojiang-chat',
                'version' => '2.0.0',
            ],
        ]);

        if (empty($result['error'])) {
            $this->initialized = true;
            $this->serverInfo = $result;
        }

        return $result;
    }

    /**
     * 获取服务端的工具列表.
     */
    public function listTools(): array
    {
        if (! $this->initialized) {
            $initResult = $this->initialize();
            if (! empty($initResult['error'])) {
                return $initResult;
            }
        }

        $result = $this->sendRequest('tools/list', []);
        if (! empty($result['error'])) {
            return $result;
        }

        return $result['tools'] ?? [];
    }

    /**
     * 调用服务端的工具.
     */
    public function callTool(string $toolName, array $arguments): array
    {
        if (! $this->initialized) {
            $initResult = $this->initialize();
            if (! empty($initResult['error'])) {
                return [
                    'content' => [['type' => 'text', 'text' => $initResult['message'] ?? 'MCP initialize failed']],
                    'isError' => true,
                ];
            }
        }

        $result = $this->sendRequest('tools/call', [
            'name'      => $toolName,
            'arguments' => $arguments,
        ]);

        if (! empty($result['error'])) {
            return [
                'content' => [['type' => 'text', 'text' => $result['message'] ?? 'Tool call failed']],
                'isError' => true,
            ];
        }

        return $result;
    }

    /**
     * 测试连接：initialize + tools/list.
     */
    public function testConnection(): array
    {
        $initResult = $this->initialize();
        if (! empty($initResult['error'])) {
            return ['success' => false, 'message' => $initResult['message'], 'tool_count' => 0];
        }

        $tools = $this->listTools();
        if (! empty($tools['error'])) {
            return ['success' => false, 'message' => $tools['message'], 'tool_count' => 0];
        }

        return [
            'success'    => true,
            'message'    => '连接成功',
            'server'     => $this->serverInfo['serverInfo'] ?? [],
            'tool_count' => count($tools),
        ];
    }
}
