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
        Schema::create('user_education_history', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 32)->default('')->index()->comment('关联用户ID');
            $table->unsignedInteger('resume_id')->default(0)->index()->comment('关联简历ID');
            $table->date('start_time')->nullable()->comment('开始时间');
            $table->date('end_time')->nullable()->comment('结束时间');
            $table->string('school_name', 200)->default('')->comment('学校名称');
            $table->string('major', 50)->default('')->comment('所学专业');
            $table->string('education', 50)->default('')->comment('学历');
            $table->string('academic', 50)->default('')->comment('学位');
            $table->string('remark')->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_education_history');
    }
};
