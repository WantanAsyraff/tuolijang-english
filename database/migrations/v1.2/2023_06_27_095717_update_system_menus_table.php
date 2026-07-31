<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_menus', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('is_del');
            $table->string('type', 5)->default('D')->index('type')->comment('类型：M、菜单；B、按钮；A、接口；')->change();
            $table->string('component',256)->default('')->comment('前端路径')->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_menus', function (Blueprint $table) {
            $table->tinyInteger('is_default')->default(0)->comment('是否为默认权限1是0否')->after('entid');
            $table->boolean('type')->default(false)->comment('1=权限0=菜单')->after('is_default');
            $table->dropColumn('component');
        });
    }
};
