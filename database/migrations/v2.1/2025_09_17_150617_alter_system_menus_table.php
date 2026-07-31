<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::table('system_menus', function (Blueprint $table) {
            $table->dropColumn(['path']);
            $table->string('paths', 255)->default('')->comment('路径');
            $table->string('uniqued', 100)->nullable()->comment('菜单唯一标识');
            $table->string('parent_uniqued', 100)->nullable()->comment('父菜单唯一标识');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('system_menus', function (Blueprint $table) {
            $table->dropColumn(['paths', 'uniqued', 'parent_uniqued']);
            $table->string('path')->length(255)->default('')->comment('路径');
        });
    }
};
