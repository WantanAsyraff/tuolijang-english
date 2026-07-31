<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('chat_app_mcp_services')) {
            Schema::create('chat_app_mcp_services', function (Blueprint $table) {
                $table->id();
                $table->string('name')->default('')->comment('服务名称');
                $table->string('info', 255)->default('')->comment('简介');
                $table->string('type', 20)->default('sse')->comment('连接类型：sse/stdio');
                $table->string('service_url', 500)->default('')->comment('MCP服务地址');
                $table->text('headers')->nullable()->comment('自定义请求头（JSON键值对）');
                $table->text('config_json')->nullable()->comment('MCP配置JSON（transport/url/headers/timeout）');
                $table->tinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
                $table->tinyInteger('is_default')->default(0)->comment('是否为默认服务：0=否，1=是');
                $table->unsignedInteger('sort')->default(0)->comment('排序');
                $table->timestamps();
                $table->index(['status', 'is_default']);
                $table->comment('聊天应用MCP服务配置表');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chat_app_mcp_services');
    }
};
