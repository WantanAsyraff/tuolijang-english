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
        Schema::create('roster_cycle_shift', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('cycle_id')->comment('周期ID');
            $table->integer('shift_id')->comment('班次ID');
            $table->integer('number')->comment('周期数');
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
        Schema::dropIfExists('roster_cycle_shift');
    }
};
