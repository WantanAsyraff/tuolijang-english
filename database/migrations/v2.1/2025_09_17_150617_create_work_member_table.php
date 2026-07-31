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
        Schema::create('work_member', function (Blueprint $table) {
            $table->id();
            $table->string('corp_id', 18)->default('')->comment('企业微信id');
            $table->string('userid', 64)->default('')->comment('成员UserID');
            $table->integer('uid')->default('0')->comment('用户id');
            $table->string('name', 64)->default('')->comment('成员名称');
            $table->string('position', 50)->default('')->comment('职务信息');
            $table->string('mobile', 11)->default('')->comment('手机号码');
            $table->integer('gender')->default('0')->comment('性别。0表示未定义，1表示男性，2表示女性');
            $table->string('email', 50)->default('')->comment('邮箱');
            $table->string('biz_mail', 50)->default('')->comment('企业邮箱');
            $table->string('direct_leader', 500)->default('')->comment('直属上级UserID');
            $table->string('avatar', 255)->default('')->comment('头像url');
            $table->string('thumb_avatar', 255)->default('')->comment('头像缩略图url');
            $table->string('telephone', 50)->default('')->comment('座机');
            $table->string('alias', 30)->default('')->comment('别名');
            $table->integer('enable')->default('0')->comment('启用/禁用成员。1表示启用成员，0表示禁用成员');
            $table->integer('is_leader')->default('0')->comment('是否是领导');
            $table->integer('hide_mobile')->default('0')->comment('是否隐藏手机号');
            $table->string('address', 255)->default('')->comment('地址');
            $table->string('open_userid', 64)->default('')->comment('全局唯一');
            $table->integer('main_department')->default('0')->comment('主部门');
            $table->integer('status')->default('0')->comment('激活状态: 1=已激活，2=已禁用，4=未激活，5=退出企业');
            $table->string('qr_code', 255)->default('')->comment('员工个人二维码');
            $table->string('external_position', 100)->default('')->comment('对外职务');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信成员');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_member');
    }
};
