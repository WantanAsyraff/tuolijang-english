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
        Schema::table('system_crud_field', function (Blueprint $table) {
            $table->integer('data_type')->default('0')->comment('0=数据字典；1=静态数据；3=数据接口');
            $table->text('customize_items')->comment('静态数据');
            $table->integer('association_show_type')->default('1')->comment('0=下拉，1=弹窗');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('system_crud_field', function (Blueprint $table) {
            $table->dropColumn(['data_type', 'customize_items', 'association_show_type']);
        });
    }
};
