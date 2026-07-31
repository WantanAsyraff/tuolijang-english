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
        Schema::create('folder_share', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('共享 id');
            $table->unsignedBigInteger('folder_id')->comment('文件 id');
            $table->unsignedBigInteger('auth_id')->comment('权限 id');
            $table->char('to_uid', 32)->comment('共享用户');
            $table->unsignedBigInteger('entid')->nullable()->default(0)->comment('企业 id/用户 id');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent()->comment('共享时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_share');
    }
};
