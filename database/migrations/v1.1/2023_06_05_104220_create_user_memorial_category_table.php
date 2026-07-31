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
        Schema::create('user_memorial_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->string('path')->default('')->comment('路径');
            $table->string('name', 120)->default('')->comment('分类名称');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->unsignedTinyInteger('types')->default(1)->comment('类型：0、默认；1、用户添加');
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
        Schema::dropIfExists('user_memorial_category');
    }
};
