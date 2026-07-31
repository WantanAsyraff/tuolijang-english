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
        Schema::create('system_backup', function (Blueprint $table) {
            $table->increments('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('path', 120)->default('')->comment('文件路径');
            $table->string('uid', 32)->nullable()->default('')->comment('创建用户ID');
            $table->string('version')->nullable()->default('')->comment('版本号');
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
        Schema::dropIfExists('system_backup');
    }
};
