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
        Schema::create('enterprise_paytype', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('type_id')->default(0)->comment('支付方式ID');
            $table->string('name', 32)->default('')->comment('支付方式名称');
            $table->string('ident', 50)->default('')->comment('支付方式标识');
            $table->string('info')->nullable()->default('')->comment('简介');
            $table->tinyInteger('status')->default(1)->comment('是否可用：1、是；0、否；');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('enterprise_paytype');
    }
};
