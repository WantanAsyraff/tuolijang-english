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
        Schema::create('attendance_statistics_leave', function (Blueprint $table) {
            $table->integer('id', true)->comment('自增ID');
            $table->bigInteger('statistics_id')->comment('考勤记录ID');
            $table->integer('apply_record_id')->comment('申请记录ID');
            $table->integer('uid')->comment('考勤人员ID');
            $table->string('type_unique', 50)->comment('请假类型');
            $table->decimal('leave_duration', 5)->comment('请假工时');
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
        Schema::dropIfExists('attendance_statistics_leave');
    }
};
