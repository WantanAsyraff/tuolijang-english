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
        Schema::create('user_token', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->string('uid', 32)->default('')->comment('关联用户UID');
            $table->string('client', 32)->default('')->comment('登陆客户端名称');
            $table->string('last_ip', 32)->default('')->comment('登陆IP');
            $table->string('mac', 32)->default('')->comment('登陆MAC地址');
            $table->text('last_token')->nullable()->comment('上次过期token');
            $table->text('remember_token')->nullable()->comment('当前登陆token');
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
        Schema::dropIfExists('user_token');
    }
};
