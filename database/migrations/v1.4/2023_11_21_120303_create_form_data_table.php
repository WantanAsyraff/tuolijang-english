<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_data', function (Blueprint $table) {
            $table->id();
            $table->string('key', 32)->comment('字段唯一值');
            $table->string('key_name', 256)->default('')->comment('字段名称');
            $table->string('type', 100)->default('')->comment('类型(文本框,单选按钮...)');
            $table->string('input_type', 20)->default('input')->comment('表单类型');
            $table->unsignedInteger('cate_id')->default(0)->comment('配置分类id');
            $table->string('param')->default('')->comment('规则 单选框和多选框');
            $table->tinyInteger('decimal_place')->default(0)->comment('数字字段小数位数');
            $table->tinyInteger('upload_type')->default(0)->comment('上传文件格式1单图2多图3文件');
            $table->string('required')->default('')->comment('是否必填：1、必填；0、非必填；');
            $table->string('placeholder', 256)->default('')->comment('提示文字');
            $table->integer('max')->default(0)->comment('最大边界值');
            $table->integer('min')->default(0)->comment('最小边界值');
            $table->string('dict_ident', 256)->default(0)->comment('字典标识');
            $table->string('value', 256)->default('')->comment('默认值');
            $table->unsignedTinyInteger('uniqued')->default(0)->comment('是否校验唯一');
            $table->string('desc')->default('')->comment('配置简介');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：1、显示；2、隐藏；');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_data');
    }
};
