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
        Schema::create('schedule', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('uid')->default(0)->comment('企业用户ID');
            $table->integer('cid')->default(0)->comment('日程分类ID');
            $table->string('color', 32)->default('')->comment('日程分类颜色');
            $table->string('title', 256)->default('')->comment('日程标题');
            $table->text('content')->comment('日程内容');
            $table->unsignedInteger('all_day')->default(0)->comment('是否全天：1、是；0、否；');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->unsignedTinyInteger('period')->default(0)->comment('重复周期：0、不重复；1、日；2、月；3、年；');
            $table->unsignedTinyInteger('rate')->default(1)->comment('重复频率');
            $table->string('days', 256)->default('')->comment('重复星期/日期');
            $table->tinyInteger('remind')->default(1)->comment('是否提醒：1、是；0、否；');
            $table->timestamp('fail_time')->nullable()->comment('结束时间');
            $table->unsignedInteger('pid')->default(0)->comment('关联日程ID');
            $table->unsignedInteger('link_id')->default(0)->comment('关联业务ID');
            $table->unsignedTinyInteger('status')->default(0)->comment('日程状态：0、待定；1、接受；2、拒绝；3、完成');
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
        Schema::dropIfExists('schedule');
    }
};
