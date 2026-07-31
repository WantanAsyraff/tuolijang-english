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
        Schema::create('enterprise_template', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('user_id')->default(0)->comment('企业用户ID');
            $table->integer('cate_id')->default(0)->comment('模板分类ID');
            $table->string('name', 128)->default('')->comment('模板名称');
            $table->text('info')->comment('模板简介');
            $table->string('cover', 256)->default('')->comment('封面图');
            $table->string('color', 32)->default('#000000')->comment('默认字体颜色');
            $table->integer('status')->default(0)->comment('开放状态：0、不开放；1、开放；');
            $table->unsignedTinyInteger('types')->default(0)->comment('记分类型：0，加权评分；1，加和评分');
            $table->unsignedTinyInteger('way')->default(0)->comment('来源：0、企业端；');
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
        Schema::dropIfExists('enterprise_template');
    }
};
