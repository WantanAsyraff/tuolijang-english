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
        Schema::create('client_follow', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->text('content')->comment('说明内容');
            $table->tinyInteger('types')->default(0)->comment('类型：0，说明；1，提醒；');
            $table->timestamp('time')->nullable()->comment('提醒时间');
            $table->string('uniqued', 32)->default('')->comment('定时任务唯一值');
            $table->tinyInteger('status')->default(0)->comment('状态：0、待处理；1、放弃；2、已完成；');
            $table->softDeletes()->comment('删除时间');
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
        Schema::dropIfExists('client_follow');
    }
};
