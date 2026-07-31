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
        Schema::create('user_enterprise', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->char('uid', 36);
            $table->unsignedBigInteger('entid')->index('entid');
            $table->integer('ident')->default(0)->comment('用户在企业中身份：-1、创建人；');
            $table->integer('card_id')->default(0)->comment('企业名片ID');
            $table->string('name', 128)->default('')->comment('昵称');
            $table->string('avatar', 256)->default('')->comment('头像');
            $table->string('phone', 15)->default('')->comment('手机号');
            $table->unsignedInteger('job')->default(0)->comment('职位ID');
            $table->string('roles', 128)->default('')->comment('角色权限');
            $table->tinyInteger('verify')->default(0)->comment('0=待审核,1=审核通过');
            $table->tinyInteger('status')->default(1)->comment('用户在企业中状态：0、锁定；1、正常');
            $table->date('join_time')->nullable()->comment('加入时间');
            $table->softDeletes()->comment('删除时间');
            $table->timestamps();

            $table->index(['entid', 'ident'], 'ent_ident');
            $table->index(['uid', 'entid'], 'u_statu');
            $table->index(['uid', 'entid'], 'uid_ent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_enterprise');
    }
};
