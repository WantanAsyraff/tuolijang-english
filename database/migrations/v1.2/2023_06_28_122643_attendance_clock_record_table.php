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
        Schema::create('attendance_clock_record', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('自增ID');
            $table->integer('frame_id')->comment('部门ID');
            $table->integer('group_id')->comment('考勤组ID');
            $table->string('group', 50)->comment('考勤组名称');
            $table->integer('shift_id')->comment('考勤班次ID');
            $table->string('shift_data', 1023)->comment('班次数据');
            $table->string('address', 100)->default('')->comment('打卡地址');
            $table->string('lat', 16)->default('')->comment('纬度');
            $table->string('lng', 16)->default('')->comment('经度');
            $table->string('remark', 120)->default('')->comment('备注');
            $table->string('image')->default('')->comment('图片');
            $table->integer('uid')->comment('考勤人员ID');
            $table->unsignedTinyInteger('is_external')->default(0)->comment('外勤打卡:0、考勤打卡；1、外勤打卡；');
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
        Schema::dropIfExists('attendance_clock_record');
    }
};
