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
        Schema::create('attendance_apply_record', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('uid')->comment('申请人');
            $table->tinyInteger('apply_type')->default(0)->comment('审批申请类型：1：请假；2：补卡；3：加班；4：外出；5：出差；');
            $table->tinyInteger('date_type')->default(0)->comment('日期类型：1：工作日；2：休息日；3：节假日；');
            $table->string('time_type', 10)->default(0)->comment('工时类型：day：天；hour：小时；minute：分钟；');
            $table->decimal('work_hours')->default(0.0)->comment('加班时长');
            $table->integer('apply_id')->comment('申请记录ID');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->string('others', 255)->default('')->comment('其他标识');
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
        Schema::dropIfExists('attendance_apply_record');
    }
};
