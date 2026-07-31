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
        Schema::create('assess_target', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('spaceid')->default(0)->comment('维度ID');
            $table->integer('ratio')->default(0)->comment('权重占比');
            $table->string('name', 256)->default('')->comment('指标名称');
            $table->text('content')->comment('指标内容');
            $table->string('finish_info', 256)->default('')->comment('完成情况');
            $table->integer('finish_ratio')->default(0)->comment('完成百分比');
            $table->string('check_info', 256)->nullable()->default('')->comment('上级评价');
            $table->integer('max')->default(0)->comment('最高得分');
            $table->integer('score')->default(0)->comment('评价得分');
            $table->softDeletes()->comment('删除时间');
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
        Schema::dropIfExists('assess_target');
    }
};
