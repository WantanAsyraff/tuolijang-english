<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('chat_app_mcp_services')) {
            return;
        }

        $now = now();
        $services = [
            'customer' => ['name' => '客户MCP服务', 'info' => '客户、线索、商机、订单、合同、发票、客户账目、联系人、跟进记录'],
            'attendance' => ['name' => '考勤MCP服务', 'info' => '考勤打卡、排班、申请和统计'],
            'assess' => ['name' => '绩效MCP服务', 'info' => '绩效列表、详情、统计和趋势'],
            'report' => ['name' => '汇报MCP服务', 'info' => '工作汇报列表、详情和统计'],
            'schedule' => ['name' => '日程MCP服务', 'info' => '日程列表和详情'],
        ];

        foreach (array_values(array_keys($services)) as $sort => $module) {
            $service = $services[$module];
            $data = [
                'name'        => $service['name'],
                'info'        => $service['info'],
                'type'        => 'sse',
                'service_url' => '/mcp/' . $module,
                'headers'     => json_encode([], JSON_UNESCAPED_UNICODE),
                'config_json' => json_encode([
                    'transport' => 'sse',
                    'module'    => $module,
                    'headers'   => [],
                    'timeout'   => 30,
                ], JSON_UNESCAPED_UNICODE),
                'status'      => 1,
                'is_default'  => 1,
                'sort'        => $sort,
                'updated_at'  => $now,
            ];

            DB::table('chat_app_mcp_services')->updateOrInsert(
                [
                    'is_default' => 1,
                    'service_url' => '/mcp/' . $module,
                ],
                $data + ['created_at' => $now]
            );
        }

        DB::table('chat_app_mcp_services')
            ->where('is_default', 1)
            ->where(function ($query) {
                $query->where('service_url', '')
                    ->orWhereNull('service_url');
            })
            ->update([
                'status'     => 0,
                'updated_at' => $now,
            ]);
    }

    public function down()
    {
        if (Schema::hasTable('chat_app_mcp_services')) {
            DB::table('chat_app_mcp_services')
                ->where('is_default', 1)
                ->whereIn('service_url', [
                    '/mcp/customer',
                    '/mcp/attendance',
                    '/mcp/assess',
                    '/mcp/report',
                    '/mcp/schedule',
                ])
                ->delete();
        }
    }
};
