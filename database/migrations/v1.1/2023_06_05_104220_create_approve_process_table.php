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
        Schema::create('approve_process', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('创建企业ID');
            $table->unsignedInteger('card_id')->default(0)->comment('创建用户名片ID');
            $table->unsignedBigInteger('approve_id')->index('eb_enterprise_approve_process_approve_id_foreign');
            $table->unsignedInteger('level')->default(1)->comment('流程级别');
            $table->unsignedTinyInteger('groups')->default(0)->comment('分组ID');
            $table->string('name', 32)->default('')->comment('节点名称');
            $table->unsignedTinyInteger('types')->comment('节点类型：0、申请人；1、审批人；2、抄送人；3、条件；4、路由；');
            $table->string('uniqued', 32)->default('')->comment('节点唯一值');
            $table->unsignedTinyInteger('settype')->default(0)->comment('审核人类型：1、指定成员；2、指定部门主管；7、连续多部门；5、申请人自己；4、申请人自选；(0、无此条件)');
            $table->tinyInteger('director_order')->default(-1)->comment('指定层级顺序：0、从上至下；1、从下至上；(-1、无此条件)');
            $table->unsignedTinyInteger('director_level')->default(0)->comment('指定主管层级/指定终点层级：1-10；(0、无此条件)');
            $table->unsignedTinyInteger('no_hander')->default(0)->comment('当前部门无负责人时：1、上级部门负责人审批；2、为空时跳过；(0、无此条件)');
            $table->text('dep_head')->nullable()->comment('指定部门负责人');
            $table->unsignedTinyInteger('self_select')->default(0)->comment('是否允许自选抄送人');
            $table->unsignedTinyInteger('select_range')->default(0)->comment('可选范围：1、不限范围；2、指定成员；(0、无此条件)');
            $table->text('user_list')->comment('指定的成员列表');
            $table->unsignedTinyInteger('select_mode')->default(0)->comment('选人方式：1、单选；2、多选；(0、无此条件)');
            $table->unsignedTinyInteger('examine_mode')->default(0)->comment('多人审批方式：1、或签；2、会签；3、依次审批；(0、无此条件)');
            $table->unsignedTinyInteger('priority')->default(0)->comment('条件优先级');
            $table->string('parent', 32)->default('')->comment('节点父级唯一值');
            $table->unsignedTinyInteger('is_child')->default(0)->comment('是否存在子节点');
            $table->unsignedTinyInteger('is_condition')->default(0)->comment('是否存在条件');
            $table->text('condition_list')->comment('条件详情');
            $table->unsignedTinyInteger('is_initial')->default(0)->comment('是否为初始数据');
            $table->text('info')->comment('数据详情');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_process');
    }
};
