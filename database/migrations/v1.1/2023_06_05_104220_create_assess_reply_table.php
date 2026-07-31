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
        Schema::create('assess_reply', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('assessid');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('user_id')->default(0)->comment('企业用户ID');
            $table->text('content')->comment('内容');
            $table->tinyInteger('is_own')->default(0)->comment('自身可见：0、否；1、是');
            $table->tinyInteger('types')->default(0)->comment('类型：0、评价；1、申诉');
            $table->tinyInteger('status')->default(0)->comment('申诉状态：0、评价；1、已处理；2、已拒绝；');
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
        Schema::dropIfExists('assess_reply');
    }
};
