<?php
declare(strict_types=1);

namespace App\Mcp;

use App\Constants\ModuleEnum;
use App\Http\Context\DataPermissionContext;
use App\Http\Service\System\ModulePermissionService;
use App\Mcp\Tools\Contracts\ToolInterface;
use Illuminate\Support\Facades\Log;

class ToolExecutor
{
    /**
     * 执行工具
     */
    public function execute(string $toolName, array $arguments): array
    {
        $tool = ToolRegistry::getTool($toolName);

        if (!$tool) {
            return [
                'error' => true,
                'message' => "Tool not found: {$toolName}",
            ];
        }

        try {
            $this->hydrateDataPermissionContext($toolName);
            return $tool->execute($arguments);
        } catch (\Throwable $e) {
            Log::channel((string) config('mcp.logging.channel', 'mcp'))->error('MCP tool execution error', [
                'tool' => $toolName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        } finally {
            DataPermissionContext::clear();
        }
    }

    /**
     * MCP 不经过模块中间件，执行前按工具所属模块初始化数据权限上下文。
     */
    protected function hydrateDataPermissionContext(string $toolName): void
    {
        DataPermissionContext::clear();

        $module = $this->resolvePermissionModule($toolName);
        if (! $module) {
            if ($this->isKnownPermissionAwareTool($toolName)) {
                Log::channel('mcp')->warning('MCP tool permission module unresolved', [
                    'tool' => $toolName,
                ]);
            }
            return;
        }

        $userId = (int) (request()->input('mcp_user_db_id', 0) ?: auth('admin')->id());
        if (! $userId) {
            return;
        }

        app(ModulePermissionService::class)->hydrateDataPermissionContext($userId, $module);
    }

    protected function resolvePermissionModule(string $toolName): ?string
    {
        $toolModule = ToolRegistry::getToolModule($toolName);
        $moduleConfig = ToolRegistry::getModuleConfig($toolModule);
        if (! empty($moduleConfig['permission_module'])) {
            return (string) $moduleConfig['permission_module'];
        }

        foreach ([
            'attendance_' => ModuleEnum::ATTENDANCE,
            'assess_'     => ModuleEnum::ASSESS,
            'schedule_'   => ModuleEnum::SCHEDULE,
            'report_'     => ModuleEnum::REPORT,
            'program_'    => ModuleEnum::PROGRAM,
        ] as $prefix => $module) {
            if (str_starts_with($toolName, $prefix)) {
                return $module;
            }
        }

        foreach ([
            'bill_',
            'contact_',
            'contract_',
            'finance_',
            'get_customer',
            'invoice_',
            'lead_',
            'list_customers',
            'opportunity_',
            'order_',
            'record_',
            'search_customers',
        ] as $prefix) {
            if (str_starts_with($toolName, $prefix)) {
                return ModuleEnum::CUSTOMER;
            }
        }

        return null;
    }

    protected function isKnownPermissionAwareTool(string $toolName): bool
    {
        foreach ([
            'personnel_',
            'my_data_scope',
            'check_data_permission',
            'my_subordinates',
            'org_',
        ] as $prefix) {
            if (str_starts_with($toolName, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 执行工具并返回标准化结果
     */
    public function executeWithResult(string $toolName, array $arguments): array
    {
        $result = $this->execute($toolName, $arguments);

        return [
            'content' => [
                [
                    'type' => 'text',
                    'text' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                ],
            ],
            'isError' => $result['error'] ?? false,
        ];
    }
}
