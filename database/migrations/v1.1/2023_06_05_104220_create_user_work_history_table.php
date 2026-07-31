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
        Schema::create('user_work_history', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 32)->default('')->index()->comment('关联用户ID');
            $table->unsignedInteger('resume_id')->default(0)->index()->comment('关联简历ID');
            $table->date('start_time')->nullable()->comment('开始时间');
            $table->date('end_time')->nullable()->comment('结束时间');
            $table->string('company', 200)->default('')->comment('所在公司');
            $table->string('position', 128)->default('')->comment('职位');
            $table->string('describe', 500)->default('')->comment('工作描述');
            $table->string('quit_reason', 500)->default('')->comment('离职原因');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_work_history');
    }
};
