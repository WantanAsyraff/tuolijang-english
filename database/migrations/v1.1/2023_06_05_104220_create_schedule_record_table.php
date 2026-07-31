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
        Schema::create('schedule_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->integer('schedule_id')->default(0)->comment('提醒ID');
            $table->tinyInteger('status')->default(1)->comment('完成状态：1、是；0、否；');
            $table->date('remind_day')->nullable()->comment('提醒日期');
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
        Schema::dropIfExists('schedule_record');
    }
};
