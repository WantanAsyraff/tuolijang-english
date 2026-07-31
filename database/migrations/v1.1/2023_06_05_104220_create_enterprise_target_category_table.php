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
        Schema::create('enterprise_target_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_target_category_entid_foreign');
            $table->string('path')->default('')->comment('路径');
            $table->string('name', 120)->default('')->comment('职级类别名称');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->integer('types')->default(0)->comment('类型：0、指标分类；1、指标模板分类；');
            $table->integer('status')->default(0)->comment('开放状态：0、不开放；1、开放；');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_target_category');
    }
};
