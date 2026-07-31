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
        Schema::create('enterprise_user_position', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('card_id')->index('eb_enterprise_user_position_card_id_foreign')->comment('企业用户信息(enterprise_user_card)ID');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->string('position', 50)->default('')->comment('职位');
            $table->string('department', 50)->default('')->comment('部门');
            $table->tinyInteger('is_admin')->default(0)->comment('身份0=普通员工;1=主管');
            $table->tinyInteger('status')->default(0)->comment('任职状态0=离职;1=任职');
            $table->string('remark')->default('')->comment('备注');
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
        Schema::dropIfExists('enterprise_user_position');
    }
};
