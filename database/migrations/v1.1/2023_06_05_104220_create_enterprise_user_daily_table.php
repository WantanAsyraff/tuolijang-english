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
        Schema::create('enterprise_user_daily', function (Blueprint $table) {
            $table->bigIncrements('daily_id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_user_daily_entid_foreign');
            $table->char('uid', 36)->index('eb_enterprise_user_daily_uid_foreign');
            $table->integer('user_id')->default(0)->index('user_id')->comment('副表(user_enterprise)ID');
            $table->string('finish', 2000)->default('')->comment('工作总结');
            $table->string('plan', 2000)->default('')->comment('工作计划');
            $table->string('mark', 2000)->default('')->comment('备注信息');
            $table->tinyInteger('status')->default(0)->comment('提交状态：0、未提交；1、已提交');
            $table->tinyInteger('types')->default(0)->comment('报告类型：0、日报；1、周报；2、月报');
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
        Schema::dropIfExists('enterprise_user_daily');
    }
};
