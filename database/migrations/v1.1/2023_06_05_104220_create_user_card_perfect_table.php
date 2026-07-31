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
        Schema::create('user_card_perfect', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('uid', 32)->default('')->comment('关联用户UID');
            $table->unsignedInteger('card_id')->default(0)->comment('关联企业用户名片ID');
            $table->string('uniqued', 32)->nullable()->comment('唯一值');
            $table->integer('total')->default(0)->comment('可操作量：-1、不限');
            $table->unsignedInteger('used')->default(0)->comment('已使用量');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0、待处理；1、已通过；2、已拒绝；');
            $table->unsignedTinyInteger('types')->default(0)->comment('是否绑定用户信息');
            $table->dateTime('fail_time')->nullable()->comment('失效时间');
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
        Schema::dropIfExists('user_card_perfect');
    }
};
