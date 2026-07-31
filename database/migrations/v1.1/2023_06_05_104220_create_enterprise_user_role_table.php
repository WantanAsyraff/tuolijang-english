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
        Schema::create('enterprise_user_role', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('权限自增id');
            $table->integer('user_id')->index()->comment('企业成员ID(user_enterprise主键ID)');
            $table->text('rules')->comment('身份管理权限(system_menus主键ID)');
            $table->text('apis')->comment('身份管理接口权限(system_menus主键ID)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_user_role');
    }
};
