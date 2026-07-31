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
        Schema::create('schedule_type', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户UID');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->string('name', 256)->default('')->comment('分类名称');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->string('color', 32)->default('')->comment('日程分类颜色');
            $table->string('info', 512)->default('')->comment('分类简介');
            $table->boolean('is_public')->default(false)->comment('是否公共分类');
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
        Schema::dropIfExists('schedule_type');
    }
};
