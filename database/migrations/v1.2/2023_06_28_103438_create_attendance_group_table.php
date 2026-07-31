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
        Schema::create('attendance_group', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('name', 50)->default('')->comment('考勤组名称');
            $table->unsignedInteger('type')->default(0)->comment('考勤类型:0、人员；1、部门；');
            $table->string('address', 100)->comment('详细地址');
            $table->string('lat', 16)->comment('纬度');
            $table->string('lng', 16)->comment('经度');
            $table->integer('effective_range')->comment('有效范围');
            $table->string('location_name', 50)->comment('考勤地点名称');
            $table->unsignedTinyInteger('repair_allowed')->comment('允许补卡');
            $table->string('repair_type', 50)->comment('补卡类型:1、缺卡;2、迟到;3、严重迟到;4、早退；');
            $table->unsignedTinyInteger('is_limit_time')->default(0)->comment('补卡时间限制:0、不限制；1、限制；');
            $table->integer('limit_time')->default(0)->comment('补卡时间');
            $table->unsignedTinyInteger('is_limit_number')->default(0)->comment('补卡次数限制:0、不限制；1、限制；');
            $table->integer('limit_number')->default(0)->comment('补卡次数');
            $table->unsignedTinyInteger('is_photo')->default(0)->comment('拍照打卡:0、不限制；1、限制；');
            $table->unsignedTinyInteger('is_external')->default(0)->comment('外勤打卡:0、不限制；1、限制；');
            $table->unsignedTinyInteger('is_external_note')->default(0)->comment('外勤打卡备注:0、不限制；1、限制；');
            $table->unsignedTinyInteger('is_external_photo')->default(0)->comment('外勤打卡拍照:0、不限制；1、限制；');
            $table->integer('uid')->comment('业务员ID');
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
        Schema::dropIfExists('attendance_group');
    }
};
