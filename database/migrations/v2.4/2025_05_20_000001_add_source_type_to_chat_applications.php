<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasColumn('chat_applications', 'source_type')) {
            Schema::table('chat_applications', function (Blueprint $table) {
                $table->tinyInteger('source_type')->default(0)->comment('数据源类型：0=标准，1=MCP')->after('is_table');
            });
        }
        if (! Schema::hasColumn('chat_applications', 'mcp_json')) {
            Schema::table('chat_applications', function (Blueprint $table) {
                $table->text('mcp_json')->nullable()->comment('MCP配置JSON');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('chat_applications', 'source_type')) {
            Schema::table('chat_applications', function (Blueprint $table) {
                $table->dropColumn('source_type');
            });
        }
        if (Schema::hasColumn('chat_applications', 'mcp_json')) {
            Schema::table('chat_applications', function (Blueprint $table) {
                $table->dropColumn('mcp_json');
            });
        }
    }
};
