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
        Schema::create('approve_rule', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('card_id')->default(0)->comment('创建用户名片ID');
            $table->unsignedBigInteger('approve_id')->index('eb_enterprise_approve_rule_approve_id_foreign');
            $table->string('range', 256)->default('')->comment('可见范围');
            $table->unsignedInteger('abnormal')->default(0)->comment('异常处理：0、自动同意；指定处理人ID；');
            $table->string('auto', 32)->default('')->comment('自动审批：0、首个节点处理，其他自动同意；1、连续审批自动同意；2、每个节点都需审批；');
            $table->string('edit', 32)->default('')->comment('修改权限：0、员工不可修改固定人员；1、不可删除固定抄送人；');
            $table->unsignedTinyInteger('recall')->default(0)->comment('异常处理：1、审批通过后允许撤销；');
            $table->string('refuse', 32)->nullable()->default('0')->comment('被拒绝后：0、返回初始，所有人重新审批；1、跳过已通过层级；');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_rule');
    }
};
