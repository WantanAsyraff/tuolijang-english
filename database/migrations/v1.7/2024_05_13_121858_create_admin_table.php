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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->char('uid', 36);
            $table->string('account', 40)->default('')->index()->comment('用户账号');
            $table->string('password', 100)->default('')->comment('用户密码');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('name', 20)->default('')->index()->comment('用户姓名');
            $table->string('phone', 32)->default('')->index()->comment('手机号');
            $table->unsignedInteger('job')->default(0)->comment('职位ID');
            $table->tinyInteger('is_admin')->default(0)->comment('是否为超级管理员');
            $table->string('roles', 256)->default('')->comment('角色权限');
            $table->boolean('uni_online')->default(false)->comment('移动端登录状态');
            $table->string('client_id', 32)->default('')->comment('连接通道ID');
            $table->string('scan_key', 256)->nullable()->default('')->comment('扫码登录参数');
            $table->string('last_ip', 45)->nullable()->default('')->comment('访问ip');
            $table->integer('login_count')->default(0)->comment('登陆次数');
            $table->tinyInteger('status')->default(1)->comment('状态：0、锁定；1、正常；');
            $table->unsignedTinyInteger('is_init')->default(1)->comment('是否为初始密码');
            $table->string('language', 32)->default('zh-cn')->comment('语言');
            $table->string('mark')->default('');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['uid'], 'uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
};
