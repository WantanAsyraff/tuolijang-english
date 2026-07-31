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
        Schema::create('schedule_reply', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->unsignedInteger('uid')->default(0)->comment('企业用户ID');
            $table->unsignedInteger('pid')->default(0)->comment('关联日程ID');
            $table->unsignedInteger('reply_id')->default(0)->comment('关联评论ID');
            $table->unsignedInteger('to_uid')->default(0)->comment('回复指定人员ID');
            $table->timestamp('start_time')->nullable()->comment('任务开始时间');
            $table->timestamp('end_time')->nullable()->comment('任务结束时间');
            $table->string('content')->default('')->comment('评论内容');
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
        Schema::dropIfExists('schedule_reply');
    }
};
