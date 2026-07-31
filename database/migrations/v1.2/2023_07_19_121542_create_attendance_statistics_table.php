~<?php

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
        Schema::create('attendance_statistics', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('自增ID');
            $table->integer('uid')->comment('考勤人员ID');
            $table->integer('frame_id')->comment('部门ID');
            $table->integer('group_id')->comment('考勤组ID');
            $table->string('group', 50)->comment('考勤组名称');
            $table->integer('shift_id')->comment('考勤班次ID');
            $table->string('shift_data', 1023)->comment('班次数据');
            $table->timestamp('one_shift_time')->nullable()->comment('一班次上班打卡时间');
            $table->tinyInteger('one_shift_is_after')->comment('当日次数：0、当日；1、次日；');
            $table->tinyInteger('one_shift_status')->default(0)->comment('打卡状态：0、无需打卡；1、正常；2、迟到；3、严重迟到；4、早退；5、缺卡；');
            $table->tinyInteger('one_shift_location_status')->default(0)->comment('地点状态:0、正常；1、外勤；2、地点异常；');
            $table->bigInteger('one_shift_record_id')->comment('打卡记录ID');
            $table->timestamp('two_shift_time')->nullable()->comment('一班次下班打卡时间');
            $table->tinyInteger('two_shift_is_after');
            $table->tinyInteger('two_shift_status')->default(0);
            $table->tinyInteger('two_shift_location_status')->default(0);
            $table->bigInteger('two_shift_record_id');
            $table->timestamp('three_shift_time')->nullable()->comment('二班次上班打卡时间');
            $table->tinyInteger('three_shift_is_after');
            $table->tinyInteger('three_shift_status')->default(0);
            $table->tinyInteger('three_shift_location_status')->default(0);
            $table->bigInteger('three_shift_record_id');
            $table->timestamp('four_shift_time')->nullable()->comment('二班次下班打卡时间');
            $table->tinyInteger('four_shift_is_after');
            $table->tinyInteger('four_shift_status')->default(0);
            $table->tinyInteger('four_shift_location_status')->default(0);
            $table->bigInteger('four_shift_record_id');
            $table->decimal('required_work_hours', 5)->default(0.0)->comment('应出勤工时');
            $table->decimal('actual_work_hours', 5)->default(0.0)->comment('实际出勤工时');
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
        Schema::dropIfExists('attendance_statistics');
    }
};
