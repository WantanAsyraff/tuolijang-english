<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_short_remind', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('shift_id')->comment('班次ID');
            $table->integer('uid')->comment('员工ID');
            $table->tinyInteger('short_type')->default(0)->comment('提醒类型：0、上班；1、下班；');
            $table->timestamp('work_time')->nullable()->comment('上班时间');
            $table->timestamp('remind_time')->nullable()->comment('推送时间');
            $table->tinyInteger('is_push')->default(0)->comment('是否推送');
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
        Schema::dropIfExists('attendance_short_remind');
    }
};
