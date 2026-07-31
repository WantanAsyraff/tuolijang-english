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
        Schema::create('system_admin', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('account', 40)->default('')->comment('管理员账号');
            $table->string('avatar')->default('')->comment('管理员头像');
            $table->string('password', 100)->default('')->comment('管理员密码');
            $table->string('real_name', 20)->default('')->comment('管理员姓名');
            $table->string('roles', 128)->default('')->comment('管理员权限(对应权限规则表主键)');
            $table->string('last_ip', 45)->default('')->comment('访问ip');
            $table->integer('login_count')->default(0)->comment('管理员登陆次数');
            $table->tinyInteger('level')->default(1)->comment('管理员级别');
            $table->tinyInteger('status')->default(1)->comment('管理员状态 1有效0无效');
            $table->timestamp('is_del')->nullable()->comment('是否删除');
            $table->timestamps();
            $table->rememberToken()->comment('TOKEN');

            $table->index(['account', 'status', 'is_del'], 'status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_admin');
    }
};
