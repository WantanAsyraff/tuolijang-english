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
        Schema::create('system_paytype', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name')->default('')->comment('支付方式名称');
            $table->string('ident')->default('')->comment('支付方式标识');
            $table->string('info', 256)->default('')->comment('简介');
            $table->tinyInteger('status')->default(1)->comment('是否可用：1、是；0、否；');
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
        Schema::dropIfExists('system_paytype');
    }
};
