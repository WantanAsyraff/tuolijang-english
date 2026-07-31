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
        Schema::create('attendance_group_member', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('group_id')->comment('考勤组ID');
            $table->integer('member')->comment('考勤类型ID');
            $table->tinyInteger('type')->default(0)->comment('考勤成员类型:0、考勤人员；1、无需考勤人员；2、考勤组负责人；3、考勤部门；');
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
        Schema::dropIfExists('attendance_group_member');
    }
};
