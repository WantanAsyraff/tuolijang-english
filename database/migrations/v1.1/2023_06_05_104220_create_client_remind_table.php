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
        Schema::create('client_remind', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('cid')->default(0)->comment('合同ID');
            $table->integer('cate_id')->default(0)->comment('续费类型ID');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('bill_id')->default(0)->comment('付款单ID');
            $table->decimal('num', 10)->default(0)->comment('金额');
            $table->text('mark')->comment('备注');
            $table->timestamp('time')->nullable()->comment('提醒时间');
            $table->timestamp('this_period')->nullable()->comment('本期时间');
            $table->timestamp('next_period')->nullable()->comment('下期时间');
            $table->string('uniqued', 32)->nullable()->comment('定时任务唯一值');
            $table->unsignedInteger('rate')->default(0)->comment('重复频率');
            $table->tinyInteger('period')->default(0)->comment('重复周期：0、天；1、周；2、月；3、年');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型：0、回款；1、续费；');
            $table->tinyInteger('status')->default(0)->comment('状态：0、正常；1、放弃；');
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
        Schema::dropIfExists('client_remind');
    }
};
