<?php

declare(strict_types=1);


return [
    /*
    |--------------------------------------------------------------------------
    | MCP Server Configuration
    |--------------------------------------------------------------------------
    */
    'server' => [
        'name'        => '陀螺匠MCP服务',
        'version'     => '2.0.0',
        'description' => '提供客户管理、人员管理、绩效管理、汇报管理、财务管理等服务的MCP接口',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'enabled'           => true,
        'middleware'        => ['auth.admin'],
        'token_expires_in'  => 7200,
        'guard'             => 'admin',
        'refresh_threshold' => env('JWT_REFRESH_THRESHOLD', 900),
        'header_auth' => [
            'enabled'         => true,
            'account_header'  => 'X-Account',
            'password_header' => 'X-Password',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled'          => true,
        'channel'          => 'mcp',
        'level'            => 'info',
        'log_success'      => (bool) env('MCP_LOG_AUTH_SUCCESS', false),
        'log_user_context' => (bool) env('MCP_LOG_USER_CONTEXT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'tools_meta_ttl' => (int) env('MCP_TOOLS_META_CACHE_TTL', 3600),
        'user_context_ttl' => (int) env('MCP_USER_CONTEXT_CACHE_TTL', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | 注意：data_scope_type 已移除，权限从用户角色中动态获取
    |
    */
    'modules' => [
        'personnel' => ['name' => '人员管理'],
        'customer' => ['name' => '客户管理'],
        'lead' => ['name' => '线索管理'],
        'order' => ['name' => '订单管理'],
        'opportunity' => ['name' => '商机管理'],
        'contract' => ['name' => '合同管理'],
        'invoice' => ['name' => '发票管理'],
        'record' => ['name' => '跟进记录'],
        'contact' => ['name' => '联系人管理'],
        'bill' => ['name' => '账目管理'],
        'assess' => ['name' => '绩效管理'],
        'report' => ['name' => '汇报管理'],
        'finance' => ['name' => '财务管理'],
        'attendance' => ['name' => '考勤管理'],
        'schedule' => ['name' => '日程管理'],
        'program' => ['name' => '项目管理'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tool Module Configuration
    |--------------------------------------------------------------------------
    |
    | 新版 MCP 按业务模块暴露工具。/mcp 保留全量工具兼容旧客户端，
    | /mcp/{module} 仅暴露 common_tools + 对应模块 tool_dirs 下的工具。
    |
    */
    'common_tools' => [
        'who_am_i',
        'my_data_scope',
        'check_data_permission',
    ],

    'tool_modules' => [
        'customer' => [
            'name'              => '客户',
            'description'       => '客户、线索、商机、订单、合同、发票、客户账目、联系人、跟进记录',
            'permission_module' => \App\Constants\ModuleEnum::CUSTOMER,
            'tool_dirs'         => ['Customer', 'Lead', 'Opportunity', 'Order', 'Contract', 'Invoice', 'Bill', 'Contact', 'Record'],
        ],
        'attendance' => [
            'name'              => '考勤',
            'description'       => '考勤打卡、排班、申请和统计',
            'permission_module' => \App\Constants\ModuleEnum::ATTENDANCE,
            'tool_dirs'         => ['Attendance'],
        ],
        'assess' => [
            'name'              => '绩效',
            'description'       => '绩效列表、详情、统计和趋势',
            'permission_module' => \App\Constants\ModuleEnum::ASSESS,
            'tool_dirs'         => ['Assess'],
        ],
        'report' => [
            'name'              => '汇报',
            'description'       => '工作汇报列表、详情和统计',
            'permission_module' => \App\Constants\ModuleEnum::REPORT,
            'tool_dirs'         => ['Report'],
        ],
        'schedule' => [
            'name'              => '日程',
            'description'       => '日程列表和详情',
            'permission_module' => \App\Constants\ModuleEnum::SCHEDULE,
            'tool_dirs'         => ['Schedule'],
        ],
    ],
];
