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
            $table->integer('menu_type')->default(0)->comment('路由类型：0、系统；1、实体；')->after('menu_path');
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
            $table->dropColumn('menu_type');
        });
    }
};
