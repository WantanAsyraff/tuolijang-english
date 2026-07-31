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
        Schema::create('attendance_handle_record', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->bigInteger('statistics_id')->comment('统计ID');
            $table->tinyInteger('shift_number')->comment('班次编号');
            $table->tinyInteger('before_status')->default(0)->comment('修改前状态');
            $table->tinyInteger('before_location_status')->default(0)->comment('修改前外勤状态');
            $table->tinyInteger('after_status')->default(0)->comment('修改后状态');
            $table->tinyInteger('after_location_status')->default(0)->comment('修改后外勤状态');
            $table->string('result', 20)->comment('打卡结果');
            $table->string('remark', 255)->default('')->comment('备注');
            $table->tinyInteger('source')->default(0)->comment('来源：0、手动修改；1、补卡申请；');
            $table->integer('uid')->comment('操作人');
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
        Schema::dropIfExists('attendance_handle_record');
    }
};
