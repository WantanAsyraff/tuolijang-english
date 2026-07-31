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
        Schema::create('attendance_arrange_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->bigInteger('arrange_id')->comment('排班ID');
            $table->integer('group_id')->comment('考勤组ID');
            $table->integer('uid')->comment('业务员ID');
            $table->integer('shift_id')->comment('班次ID');
            $table->timestamp('date')->comment('排班日期');
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
        Schema::dropIfExists('attendance_arrange_record');
    }
};
