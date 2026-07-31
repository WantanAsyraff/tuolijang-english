<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    /*
    |--------------------------------------------------------------------------
    | 默认Log通道
    |--------------------------------------------------------------------------
    |
    | 此选项定义将消息写入日志时使用的默认日志通道。
    | 此选项中指定的名称应与“通道”配置数组中定义的通道之一匹配。
    |
    */

    'default' => env('LOG_CHANNEL', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['single'],
            'ignore_exceptions' => false,
        ],
        'single' => [
            'driver'     => 'single',
            'path'       => storage_path(sprintf('logs/%s/%s/service-%s.log', now()->tz('Asia/Shanghai')->format('Y'),now()->tz('Asia/Shanghai')->format('m'),now()->tz('Asia/Shanghai')->format('d'))),
            'level'      => env('LOG_LEVEL', 'debug'),
            'permission' => 0755,
        ],

        'daily' => [
            'driver'     => 'daily',
            'path'       => storage_path('logs/service' . '.log'),
            'level'      => env('LOG_LEVEL', 'debug'),
            'days'       => 14,
            'permission' => 0755,
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => env('LOG_LEVEL', 'debug'),
            'handler'      => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level'  => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'mcp' => [
            'driver'     => 'daily',
            'path'       => storage_path('logs/mcp/mcp.log'),
            'level'      => env('LOG_LEVEL', 'debug'),
            'days'       => 14,
            'permission' => 0755,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],
];
