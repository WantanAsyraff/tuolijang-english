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
        Schema::table('openapi_key', function (Blueprint $table) {
            $table->dropColumn(['crud_auth']);
            $table->string('title', 50)->default('')->comment('对外接口名称')->change();
            $table->text('auth')->comment('接口权限ID')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('openapi_key', function (Blueprint $table) {
            $table->string('title')->length(255)->change();
            $table->text('auth')->comment('接口权限（系统）')->change();
            $table->text('crud_auth')->comment('接口权限（实体）');
        });
    }
};
