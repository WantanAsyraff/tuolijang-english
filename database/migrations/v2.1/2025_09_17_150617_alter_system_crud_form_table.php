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
        Schema::table('system_crud_form', function (Blueprint $table) {
            $table->string('name', 255)->default('')->comment('表单名称');
            $table->integer('is_master')->default('0')->comment('是否主要的表单');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('system_crud_form', function (Blueprint $table) {
            $table->dropColumn(['name', 'is_master']);
        });
    }
};
