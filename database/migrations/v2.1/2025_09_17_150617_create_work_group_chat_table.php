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
        Schema::create('work_group_chat', function (Blueprint $table) {
            $table->id();
            $table->string('corp_id', 18)->default('')->comment('企业ID');
            $table->string('chat_id', 40)->default('')->comment('客户群ID');
            $table->string('name', 255)->default('')->comment('群名');
            $table->string('owner', 64)->default('')->comment('群主ID');
            $table->integer('group_create_time')->default('0')->comment('群的创建时间');
            $table->string('notice', 255)->default('')->comment('群公告');
            $table->string('admin_list', 1000)->default('')->comment('群管理员userid');
            $table->integer('member_num')->default('0')->comment('群人数');
            $table->integer('retreat_group_num')->default('0')->comment('退群总数');
            $table->integer('status')->default('0')->comment('客户群跟进状态。\r\n0 - 跟进人正常\r\n1 - 跟进人离职\r\n2 - 离职继承中\r\n3 - 离职继承完成');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信群');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_group_chat');
    }
};
