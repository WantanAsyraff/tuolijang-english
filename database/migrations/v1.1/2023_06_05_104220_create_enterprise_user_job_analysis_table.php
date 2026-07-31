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
        Schema::create('enterprise_user_job_analysis', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('entid')->default(0)->index()->comment('企业ID');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->longText('data')->comment('分析内容');
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
        Schema::dropIfExists('enterprise_user_job_analysis');
    }
};
