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
        Schema::create('schedule_task', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->unsignedInteger('uid')->default(0)->comment('企业用户ID');
            $table->unsignedInteger('pid')->default(0)->comment('关联日程ID');
            $table->timestamp('start_time')->nullable()->comment('任务开始时间');
            $table->timestamp('end_time')->nullable()->comment('任务结束时间');
            $table->unsignedTinyInteger('status')->default(0)->comment('日程状态：0、待定；1、接受；2、拒绝；3、完成；');
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
        Schema::dropIfExists('schedule_task');
    }
};
