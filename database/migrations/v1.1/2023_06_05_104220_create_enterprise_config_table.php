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
        Schema::create('enterprise_config', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('key', 200)->comment('配置字段');
            $table->string('key_name')->default('')->comment('配置名称');
            $table->string('type', 100)->default('')->comment('类型(文本框,单选按钮...)');
            $table->string('input_type', 20)->default('input')->comment('表单类型');
            $table->string('category', 32)->default('')->comment('配置分类:assess、绩效考核');
            $table->string('parameter')->default('')->comment('规则 单选框和多选框');
            $table->tinyInteger('upload_type')->default(0)->comment('上传文件格式1单图2多图3文件');
            $table->string('required')->default('')->comment('规则');
            $table->integer('width')->default(0)->comment('多行文本框的宽度');
            $table->integer('high')->default(0)->comment('多行文框的高度');
            $table->string('value', 5000)->default('')->comment('默认值');
            $table->string('desc')->default('')->comment('配置简介');
            $table->integer('sort')->default(0)->comment('排序');
            $table->unsignedBigInteger('entid')->index();
            $table->tinyInteger('is_show')->default(0)->comment('是否隐藏');
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
        Schema::dropIfExists('enterprise_config');
    }
};
