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
        Schema::create('roster_cycle', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->unsignedInteger('group_id')->comment('考勤组ID');
            $table->string('name', 50)->default('')->comment('周期名称');
            $table->unsignedInteger('cycle')->default(0)->comment('周期');
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
        Schema::dropIfExists('roster_cycle');
    }
};
