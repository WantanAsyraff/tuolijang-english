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
        Schema::table('system_crud', function (Blueprint $table) {
            $table->tinyInteger('is_form_table')->default(0)->comment('是否存在表格');
            $table->json('table_field')->comment('表格提交字段和展示字段');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('system_crud', function (Blueprint $table) {
            $table->dropColumn(['is_form_table']);
            $table->dropColumn(['table_field']);
        });
    }
};
