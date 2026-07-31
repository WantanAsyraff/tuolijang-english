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
        Schema::create('system_role', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('权限自增id');
            $table->string('role_name', 32)->default('')->comment('身份管理名称');
            $table->text('rules')->comment('身份管理权限(system_menus主键ID)');
            $table->text('apis')->comment('身份管理接口权限(system_menus主键ID)');
            $table->string('type', 30)->default('')->comment('超级角色类型,空表示总后台');
            $table->integer('entid')->default(0)->comment('0=总后台,非0为企业后台');
            $table->tinyInteger('level')->default(0)->comment('等级');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->string('uniqued', 36)->nullable()->comment('企业唯一值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_role');
    }
};
