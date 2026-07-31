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
        Schema::create('attendance_whitelist', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('uid')->comment('业务员ID');
            $table->unsignedInteger('type')->default(0)->comment('人员类型:0、人员；1、管理员；');
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
        Schema::dropIfExists('attendance_whitelist');
    }
};
