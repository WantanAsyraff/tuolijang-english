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
        Schema::create('work_group_chat_member', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id')->default('0')->comment('企业群ID');
            $table->string('userid', 64)->default('')->comment('群成员id');
            $table->integer('type')->default('0')->comment('成员类型。1 - 企业成员2 - 外部联系人');
            $table->string('unionid', 64)->default('')->comment('微信开放平台的唯一身份标识（微信unionid）');
            $table->integer('join_time')->default('0')->comment('入群时间');
            $table->integer('join_scene')->default('0')->comment('入群方式。1 - 由群成员邀请入群（直接邀请入群）2 - 由群成员邀请入群（通过邀请链接入群）3 - 通过扫描群二维码入群');
            $table->string('invitor_userid', 64)->default('')->comment('邀请者。目前仅当是由本企业内部成员邀请入群时会返回该值');
            $table->string('group_nickname', 100)->default('')->comment('在群里的昵称');
            $table->string('name', 100)->default('')->comment('名字。仅当 need_name = 1 时返回');
            $table->integer('status')->default('1')->comment('1=在群中,0=已退群');
            $table->integer('chat_sum')->default('0')->comment('当前群人数');
            $table->integer('retreat_chat_num')->default('0')->comment('当前退群人数');
            $table->string('state', 100)->default('');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信群成员列表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_group_chat_member');
    }
};
