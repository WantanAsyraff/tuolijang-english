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
        Schema::create('user', function (Blueprint $table) {
            $table->char('uid', 36)->primary()->comment('用户uid');
            $table->string('account', 40)->default('')->index()->comment('用户账号');
            $table->string('password', 100)->default('')->comment('用户密码');
            $table->string('only_pwd', 100)->default('')->comment('用户密码');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('real_name', 20)->default('')->index()->comment('用户真实姓名');
            $table->string('education', 100)->default('')->comment('学历');
            $table->string('nation', 20)->default('汉')->comment('民族');
            $table->string('birthplace', 20)->default('')->comment('籍贯');
            $table->string('card_id', 18)->default('')->comment('身份证号码');
            $table->string('province', 20)->default('')->comment('现住所在省');
            $table->string('city', 20)->default('')->comment('现住所在城市');
            $table->string('area', 20)->default('')->comment('现住所在区');
            $table->string('current_address')->default('')->comment('现住地');
            $table->string('home_address')->default('')->comment('家庭住址');
            $table->string('telephone', 20)->default('')->comment('电话');
            $table->string('phone', 11)->default('')->index()->comment('手机号');
            $table->string('email', 50)->default('')->comment('邮箱');
            $table->string('standby_contacts', 20)->default('')->comment('备用联系人名');
            $table->string('standby_contacts_phone', 11)->default('')->comment('备用联系人手机号');
            $table->string('bank', 50)->default('')->comment('开户行');
            $table->string('bank_number', 21)->default('')->comment('银行卡号');
            $table->integer('age')->default(0)->comment('年龄');
            $table->unsignedInteger('entid')->default(0)->index()->comment('当前登陆所在企业ID');
            $table->string('last_ip', 45)->nullable()->default('')->comment('访问ip');
            $table->boolean('uni_online')->default(false)->comment('移动端登录状态');
            $table->string('client_id', 32)->default('')->comment('连接通道ID');
            $table->string('scan_key', 256)->nullable()->default('')->comment('扫码登录参数');
            $table->timestamp('birthday')->nullable()->comment('生日');
            $table->integer('login_count')->default(0)->comment('登陆次数');
            $table->tinyInteger('marriage')->default(0)->comment('是否结婚 0 = 未结婚,1=结婚');
            $table->tinyInteger('sex')->default(0)->comment('性别 0=未知,1=男,2=女,3=其他');
            $table->tinyInteger('status')->default(1)->comment('状态：0、锁定；1、正常；');
            $table->unsignedTinyInteger('is_init')->default(1)->comment('是否为初始密码');
            $table->string('language', 32)->default('zh-cn')->comment('语言');
            $table->string('remark')->default('');
            $table->timestamp('delete')->nullable()->comment('是否删除');
            $table->timestamps();

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
        Schema::dropIfExists('user');
    }
};
