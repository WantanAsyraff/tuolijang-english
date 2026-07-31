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
        Schema::create('folder_auth', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('文件权限 id');
            $table->unsignedBigInteger('folder_id')->comment('文件 id');
            $table->char('uid', 32)->comment('用户 id');
            $table->unsignedTinyInteger('create')->default(0)->comment('目录管理权限');
            $table->unsignedTinyInteger('read')->default(1)->comment('查看权限');
            $table->unsignedTinyInteger('update')->default(0)->comment('编辑权限');
            $table->unsignedTinyInteger('download')->default(0)->comment('下载权限');
            $table->unsignedTinyInteger('delete')->default(0)->comment('删除权限');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_auth');
    }
};
