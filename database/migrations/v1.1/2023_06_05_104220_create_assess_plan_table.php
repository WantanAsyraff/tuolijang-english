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
        Schema::create('assess_plan', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_assess_plan_entid_foreign');
            $table->boolean('create_time')->default(true)->comment('星期:1-7/或者几号1-31');
            $table->integer('create_month')->default(0)->comment('月');
            $table->boolean('assess_type')->default(false)->comment('被考核人类型：0=人员添加,1=部门添加');
            $table->tinyInteger('period')->default(0)->comment('周期:1=周;2=月;3=年;5=季度;4=半年');
            $table->enum('make_type', ['before', 'after', 'start'])->default('before')->comment('目标制定时间类型：考核开始前、考核开始后');
            $table->integer('make_day')->default(0)->comment('目标制定天数');
            $table->enum('eval_type', ['before', 'after', 'start'])->default('before')->comment('上级评价时间类型：考核结束前、考核结束后');
            $table->integer('eval_day')->default(0)->comment('上级评价天数');
            $table->enum('verify_type', ['before', 'after'])->default('after')->comment('审核时间类型：评价结束前、评价结束后');
            $table->integer('verify_day')->default(0)->comment('绩效审核天数');
            $table->tinyInteger('status')->default(0)->comment('状态:0=禁用;1=开启');
            $table->string('uniqued', 32)->default('')->comment('任务唯一值');
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
        Schema::dropIfExists('assess_plan');
    }
};
