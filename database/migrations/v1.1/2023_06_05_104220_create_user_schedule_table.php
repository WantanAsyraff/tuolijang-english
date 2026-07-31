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
        Schema::create('user_schedule', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->string('types', 32)->default('')->comment('类型：user、用户；assess、考核；');
            $table->string('content', 256)->default('')->comment('待办内容');
            $table->string('mark', 256)->default('')->comment('备注信息');
            $table->tinyInteger('remind')->default(1)->comment('是否提醒：1、是；0、否；');
            $table->tinyInteger('repeat')->default(0)->comment('是否重复：1、是；0、否；');
            $table->tinyInteger('period')->default(0)->comment('重复周期：0、天；1、周；2、月；3、年');
            $table->integer('rate')->default(1)->comment('重复频率');
            $table->date('remind_day')->nullable()->comment('提醒日期');
            $table->time('remind_time')->nullable()->comment('提醒时间');
            $table->string('days', 256)->default('')->comment('重复星期/天');
            $table->timestamp('end_time')->nullable()->comment('结束日期：0、用不结束；');
            $table->unsignedInteger('link_id')->default(0)->comment('关联ID');
            $table->string('uniqued', 32)->default('')->comment('定时任务唯一值');
            $table->timestamp('last_time')->nullable()->comment('上次提醒日期');
            $table->boolean('is_remind')->default(false)->comment('是否提醒过0=无，1=有');
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
        Schema::dropIfExists('user_schedule');
    }
};
