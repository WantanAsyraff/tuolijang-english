<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('work_client', function (Blueprint $table) {
            $table->id();
            $table->string('corp_id', 18)->default('')->comment('企业微信id');
            $table->string('external_userid', 64)->default('')->comment('外部联系人的userid');
            $table->integer('uid')->default('0')->comment('商城用户uid');
            $table->string('userid', 64)->default('')->comment('添加了此外部联系人的企业成员userid');
            $table->string('name', 50)->default('')->comment('外部联系人的名称');
            $table->string('avatar', 255)->default('')->comment('外部联系人头像');
            $table->integer('type')->default('0')->comment('1表示该外部联系人是微信用户，2表示该外部联系人是企业微信用户');
            $table->integer('gender')->default('0')->comment('性别 0-未知 1-男性 2-女性');
            $table->string('unionid', 64)->default('')->comment('开放平台的唯一身份标识');
            $table->string('position', 50)->default('')->comment('外部联系人的职位');
            $table->string('corp_name', 50)->default('')->comment('外部联系人所在企业的简称');
            $table->string('corp_full_name', 100)->default('')->comment('外部联系人所在企业的主体名称');
            $table->text('external_profile')->comment('外部联系人的详情');
            $table->string('remark', 255)->default('')->comment('备注信息');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信客户');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_client');
    }
};
