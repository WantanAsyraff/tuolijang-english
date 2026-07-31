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
        Schema::create('attendance_shift', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('name', 50)->default('')->comment('班次名称');
            $table->tinyInteger('number')->default(1)->comment('上下班次数 0、休息；1、1次上下班；2、2次上下班；');
            $table->tinyInteger('rest_time')->default(0)->comment('中途休息：1、开启；0、关闭；');
            $table->string('rest_start', 50)->default('')->comment('休息开始时间');
            $table->string('rest_end', 50)->default('')->comment('休息结束时间');
            $table->tinyInteger('rest_start_after')->default(0)->comment('休息开始规则 0、当日；1、次日；');
            $table->tinyInteger('rest_end_after')->default(0)->comment('休息结束规则 0、当日；1、次日；');
            $table->integer('overtime')->default(0)->comment('加班起算时间');
            $table->string('work_time', 50)->default('')->comment('工作时长');
            $table->string('color', 50)->default('')->comment('颜色标识');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('attendance_shift');
    }
};
