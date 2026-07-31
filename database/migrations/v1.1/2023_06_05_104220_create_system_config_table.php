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
        Schema::create('system_config', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('key', 200)->comment('配置字段');
            $table->string('key_name')->default('')->comment('配置名称');
            $table->string('type', 100)->default('')->comment('类型(文本框,单选按钮...)');
            $table->string('input_type', 20)->default('input')->comment('表单类型');
            $table->integer('cate_id')->default(0)->comment('配置分类id');
            $table->string('path')->default('')->comment('配置分类路径');
            $table->string('parameter')->default('')->comment('规则 单选框和多选框');
            $table->tinyInteger('upload_type')->default(0)->comment('上传文件格式1单图2多图3文件');
            $table->string('required')->default('')->comment('规则');
            $table->integer('width')->default(0)->comment('多行文本框的宽度');
            $table->integer('high')->default(0)->comment('多行文框的高度');
            $table->string('value', 5000)->default('')->comment('默认值');
            $table->string('desc')->default('')->comment('配置简介');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('entid')->default(0)->comment('0=总后台,1=分后台');
            $table->string('ent_key', 64)->default('')->comment('分后台');
            $table->tinyInteger('is_show')->default(0)->comment('是否隐藏');
            $table->timestamps();

            $table->unique(['key', 'entid'], 'ent_key');
            $table->index(['key', 'cate_id', 'entid'], 'key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_config');
    }
};
