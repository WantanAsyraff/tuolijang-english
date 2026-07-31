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
        Schema::create('attendance_remind', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('shift_id')->comment('班次ID');
            $table->tinyInteger('shift_num')->default(0)->comment('打卡班次数量');
            $table->timestamp('one_shift_time')->nullable()->comment('一班次上班时间');
            $table->timestamp('one_shift_remind')->nullable()->comment('一班次上班提醒');
            $table->tinyInteger('one_shift_remind_push')->default(0)->comment('一班次上班是否推送');
            $table->timestamp('one_shift_remind_short')->nullable()->comment('一班次上班缺卡提醒');
            $table->timestamp('two_shift_time')->nullable()->comment('一班次下班时间');
            $table->timestamp('two_shift_remind')->nullable()->comment('一班次下班提醒');
            $table->tinyInteger('two_shift_remind_push')->default(0)->comment('一班次下班是否推送');
            $table->timestamp('two_shift_remind_short')->nullable()->comment('一班次下班缺卡提醒');
            $table->timestamp('three_shift_time')->nullable()->comment('二班次上班时间');
            $table->timestamp('three_shift_remind')->nullable()->comment('二班次上班提醒');
            $table->tinyInteger('three_shift_remind_push')->default(0)->comment('二班次上班是否推送');
            $table->timestamp('three_shift_remind_short')->nullable()->comment('二班次上班缺卡提醒');
            $table->timestamp('four_shift_time')->nullable()->comment('二班次下班时间');
            $table->timestamp('four_shift_remind')->nullable()->comment('二班次下班提醒');
            $table->tinyInteger('four_shift_remind_push')->default(0)->comment('二班次下班是否推送');
            $table->timestamp('four_shift_remind_short')->nullable()->comment('二班次下班缺卡提醒');
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
        Schema::dropIfExists('attendance_remind');
    }
};
