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
        Schema::create('attendance_shift_rule', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('shift_id')->comment('班次表ID');
            $table->tinyInteger('number')->default(1)->comment('次数 1、1次上下班；2、2次上下班；');
            $table->tinyInteger('first_day_after')->default(0)->comment('上班当日次数 0、当日；1、次日；');
            $table->tinyInteger('second_day_after')->default(0)->comment('下班当日次数 0、当日；1、次日；');
            $table->string('work_hours')->default('')->comment('上班时间');
            $table->integer('late')->default(0)->comment('迟到');
            $table->integer('extreme_late')->default(0)->comment('严重迟到');
            $table->integer('late_lack_card')->default(0)->comment('晚到缺卡');
            $table->integer('early_card')->default(0)->comment('提前打卡');
            $table->string('off_hours')->default('')->comment('下班时间');
            $table->integer('early_leave')->default(0)->comment('早退');
            $table->integer('early_lack_card')->default(0)->comment('提前缺卡');
            $table->integer('delay_card')->default(0)->comment('延后打卡');
            $table->tinyInteger('free_clock')->default(0)->comment('下班可免打卡');
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
        Schema::dropIfExists('attendance_shift_rule');
    }
};
