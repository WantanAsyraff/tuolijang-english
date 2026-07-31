<?php
declare(strict_types=1);

namespace App\Mcp\Tools\Traits;

use App\Http\Controller\Mcp\McpRequest;

trait HasMcpContext
{
    protected McpRequest $mcpRequest;

    public function setMcpRequest(McpRequest $mcpRequest): void
    {
        $this->mcpRequest = $mcpRequest;
    }

    protected function getUserId(): int
    {
        return $this->mcpRequest->getUserId();
    }

    protected function getUserInfo(): array
    {
        return $this->mcpRequest->getUserInfo();
    }

    protected function isAdmin(): bool
    {
        return $this->mcpRequest->isAdmin();
    }
}
