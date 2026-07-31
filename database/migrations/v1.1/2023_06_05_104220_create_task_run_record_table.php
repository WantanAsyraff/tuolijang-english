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
        Schema::create('task_run_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('task_id');
            $table->string('message')->default('')->comment('错误提示');
            $table->integer('line')->default(0)->comment('错误行数');
            $table->string('files')->default('')->comment('错误文件');
            $table->tinyInteger('status')->default(0)->comment('1=执行成功;0=执行失败;-1=未执行');
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
        Schema::dropIfExists('task_run_record');
    }
};
