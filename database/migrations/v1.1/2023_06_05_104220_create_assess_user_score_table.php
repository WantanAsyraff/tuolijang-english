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
        Schema::create('assess_user_score', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->integer('assessid')->default(0)->comment('考核记录ID');
            $table->integer('userid')->default(0)->comment('操作人ID');
            $table->unsignedInteger('check_uid')->default(0)->comment('考核人ID');
            $table->unsignedInteger('test_uid')->default(0)->comment('被考核人ID');
            $table->decimal('score', 5)->default(0)->comment('考核得分');
            $table->decimal('total', 5)->default(0)->comment('最高分');
            $table->unsignedInteger('grade')->nullable()->default(0)->comment('考核等级');
            $table->text('info')->comment('变更说明');
            $table->string('mark', 32)->default('')->comment('备注信息');
            $table->unsignedTinyInteger('types')->default(0)->comment('操作类型：0、评分；1、删除绩效；');
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
        Schema::dropIfExists('assess_user_score');
    }
};
