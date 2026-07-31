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
        Schema::create('enterprise_role', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('角色自增id');
            $table->string('role_name', 32)->comment('角色名称');
            $table->string('types', 30)->nullable()->comment('角色类型，null为用户自己添加');
            $table->integer('user_count')->default(0)->comment('用户数量');
            $table->integer('entid')->index()->comment('企业ID');
            $table->text('rules')->comment('身份管理权限(system_menus主键ID)');
            $table->text('rule_unique')->nullable()->comment('菜单标识');
            $table->text('apis')->comment('身份管理接口权限(system_menus主键ID)');
            $table->text('api_unique')->nullable()->comment('接口标识');
            $table->tinyInteger('status')->default(0)->comment('状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_role');
    }
};
