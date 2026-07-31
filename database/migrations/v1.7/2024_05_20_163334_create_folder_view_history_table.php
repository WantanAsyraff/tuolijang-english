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
        Schema::create('folder_view_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->default(0)->comment('用户id');
            $table->char('uid', 32)->comment('修改用户');
            $table->unsignedBigInteger('folder_id')->comment('文件 id');
            $table->string('file_name')->comment('文件真实名称');
            $table->string('file_url')->comment('文件 url');
            $table->timestamps();
            $table->index('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_view_history');
    }
};
