<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('contract', 'area_cascade')) {
            Schema::table('contract', function (Blueprint $table) {
                $table->json('area_cascade')->nullable()->comment('省市区');
            });
        }
        if (! Schema::hasColumn('system_crud', 'show_log')) {
            Schema::table('system_crud', function (Blueprint $table) {
                $table->tinyInteger('show_log')->default(0)->comment('是否展示日志')->after('is_update_table');
            });
        }
        if (! Schema::hasColumn('system_crud', 'comment_title')) {
            Schema::table('system_crud', function (Blueprint $table) {
                $table->string('comment_title', 50)->default('')->comment('评论标题')->after('show_log');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down() {}
};
