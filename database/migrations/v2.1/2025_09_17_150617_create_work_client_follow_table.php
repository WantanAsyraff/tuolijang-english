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
        Schema::create('work_client_follow', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->default('0')->comment('客户id');
            $table->string('userid', 64)->default('')->comment('添加了此外部联系人的企业成员userid');
            $table->string('remark', 50)->default('')->comment('该成员对此外部联系人的备注');
            $table->string('description', 255)->default('')->comment('该成员对此外部联系人的描述');
            $table->integer('createtime')->default('0')->comment('该成员添加此外部联系人的时间');
            $table->string('remark_corp_name', 50)->default('')->comment('该成员对此微信客户备注的企业名称');
            $table->string('remark_mobiles', 255)->default('')->comment('该成员对此客户备注的手机号码');
            $table->integer('add_way')->default('0')->comment('该成员添加此客户的来源');
            $table->string('oper_userid', 64)->default('')->comment('发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid');
            $table->string('state', 30)->default('')->comment('自定义字段返回数据');
            $table->integer('is_del_user')->default('0')->comment('客户是否删除跟踪人:0=没有,1=删除');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信客服跟踪');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_client_follow');
    }
};
