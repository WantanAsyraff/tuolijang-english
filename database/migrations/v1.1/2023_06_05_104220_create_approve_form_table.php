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
        Schema::create('approve_form', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('card_id')->default(0)->comment('创建用户名片ID');
            $table->unsignedBigInteger('approve_id')->index('eb_enterprise_approve_form_approve_id_foreign');
            $table->string('title', 32)->default('')->comment('表单名称');
            $table->string('info', 64)->default('')->comment('表单提示');
            $table->string('value', 64)->default('')->comment('表单默认值');
            $table->unsignedTinyInteger('required')->default(0)->comment('是否必选');
            $table->string('types', 32)->default('')->comment('表单类型');
            $table->text('content')->comment('表单详情');
            $table->text('props')->comment('限制条件');
            $table->text('options')->comment('表单配置信息');
            $table->text('config')->comment('表单配置信息');
            $table->string('uniqued')->default('')->comment('表单唯一值');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_form');
    }
};
