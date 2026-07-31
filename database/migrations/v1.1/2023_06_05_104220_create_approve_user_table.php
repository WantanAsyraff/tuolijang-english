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
        Schema::create('approve_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('card_id')->default(0)->comment('相关用户名片ID');
            $table->unsignedBigInteger('approve_id')->index('eb_enterprise_approve_user_approve_id_foreign');
            $table->unsignedBigInteger('apply_id')->index('eb_enterprise_approve_user_apply_id_foreign');
            $table->string('node_id', 32)->default('')->comment('审核节点ID(唯一值)');
            $table->unsignedTinyInteger('level')->default(1)->comment('级别');
            $table->unsignedTinyInteger('sort')->default(1)->comment('审批顺序');
            $table->unsignedTinyInteger('verify')->default(0)->comment('操作状态：0、自动；1、手动；');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0、待审核/失效；1、已通过/有效；2、未通过；');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型：1、审核人；2、抄送人；');
            $table->text('info')->comment('人员详情');
            $table->text('process_info')->comment('流程节点详情');
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
        Schema::dropIfExists('approve_user');
    }
};
