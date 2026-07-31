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
        Schema::create('attendance_group_shift', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->unsignedInteger('group_id')->comment('考勤组ID');
            $table->unsignedInteger('shift_id')->comment('班次表ID');
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
        Schema::dropIfExists('attendance_group_shift');
    }
};
