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
        Schema::create('enterprise_user_education', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('card_id')->index('eb_enterprise_user_education_card_id_foreign')->comment('企业用户信息(enterprise_user_card)ID');
            $table->date('start_time')->nullable()->comment('开始时间');
            $table->date('end_time')->nullable()->comment('结束时间');
            $table->string('school_name', 200)->default('')->comment('学校名称');
            $table->string('major', 50)->default('')->comment('所学专业');
            $table->string('education', 50)->default('')->comment('学历');
            $table->string('academic', 50)->default('')->comment('学位');
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
        Schema::dropIfExists('enterprise_user_education');
    }
};
