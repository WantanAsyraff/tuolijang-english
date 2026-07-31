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
        Schema::create('system_quick', function (Blueprint $table) {
            $table->comment('快捷入口表');
            $table->increments('id')->comment('自增ID');
            $table->string('name', 50)->default('')->comment('标题名称');
            $table->unsignedInteger('cid')->default(0)->comment('分类id');
            $table->string('pc_url', 120)->default('')->comment('PC端地址');
            $table->string('uni_url', 120)->default('')->comment('移动端地址');
            $table->string('image')->default('')->comment('图标');
            $table->unsignedInteger('sort')->default(0)->comment('排序，数字越大越在前面');
            $table->tinyInteger('types')->default(1)->comment('菜单类型 0:个人菜单 1:企业菜单');
            $table->tinyInteger('pc_show')->default(0)->comment('PC端显示 0:隐藏 1:显示');
            $table->tinyInteger('uni_show')->default(0)->comment('移动端显示 0:隐藏 1:显示');
            $table->tinyInteger('status')->default(1)->comment('状态 0:隐藏 1:显示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_quick');
    }
};
