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
        Schema::create('approve_holiday_type', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('name', 50)->default('')->comment('假期类型');
            $table->tinyInteger('new_employee_limit')->default(0)->comment('新员工请假限制：0、不限制；1、限制；');
            $table->tinyInteger('new_employee_limit_month')->default(1)->comment('新员工请假月时限制');
            $table->tinyInteger('duration_type')->default(0)->comment('请假时长类型：0、天；1、小时；');
            $table->tinyInteger('duration_calc_type')->default(1)->comment('时长计算类型：0、自然日；1、工作日；');
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
        Schema::dropIfExists('approve_holiday_type');
    }
};
