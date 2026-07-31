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
        Schema::create('client_liaison', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->string('name', 32)->default('')->comment('联系人姓名');
            $table->string('job', 32)->default('')->comment('联系人职务');
            $table->integer('gender')->default(0)->comment('性别: 0、未知；1、男；2、女；3、其他；');
            $table->string('tel', 32)->default('')->comment('电话');
            $table->string('mail', 64)->default('')->comment('邮箱');
            $table->string('wechat', 60)->default('')->comment('微信');
            $table->string('mark', 256)->default('')->comment('备注');
            $table->string('creator', 36)->default('')->comment('创建人ID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_liaison');
    }
};
