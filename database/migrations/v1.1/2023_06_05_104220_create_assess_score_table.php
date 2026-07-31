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
        Schema::create('assess_score', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_assess_score_entid_index');
            $table->integer('user_id')->default(0)->index('eb_enterprise_assess_score_user_id_index')->comment('用户关联企业表(user_enterprise主键)ID');
            $table->string('name', 32)->default('')->comment('等级名称');
            $table->unsignedInteger('min')->default(0)->comment('分数最小值');
            $table->unsignedInteger('max')->default(0)->comment('分数最大值');
            $table->tinyInteger('level')->default(0)->comment('级别');
            $table->string('mark', 32)->default('')->comment('说明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assess_score');
    }
};
