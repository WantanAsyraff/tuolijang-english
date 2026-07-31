<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('work_mass_messaging', function (Blueprint $table) {
            $table->comment('企微消息群发任务表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型:1、群聊消息,0、群发消息,2、朋友圈消息;');
            $table->unsignedTinyInteger('is_all')->default(0)->comment('是否全部');
            $table->json('send_uid')->nullable()->comment('发送用户ID');
            $table->json('send_group')->nullable()->comment('发送群聊ID');
            $table->json('send_customer')->nullable()->comment('发送客户ID');
            $table->json('search')->nullable()->comment('搜索条件');
            $table->unsignedTinyInteger('is_modify')->default(0)->comment('是否允许修改');
            $table->unsignedInteger('temp_id')->default(0)->comment('素材模板ID');
            $table->unsignedTinyInteger('is_timed')->default(0)->comment('是否定时发送');
            $table->timestamp('send_time')->nullable()->comment('发送时间');
            $table->unsignedInteger('be_sent')->default(0)->comment('待发送');
            $table->unsignedInteger('is_send')->default(0)->comment('已发送');
            $table->unsignedInteger('is_sent')->default(0)->comment('已送达');
            $table->unsignedInteger('not_sent')->default(0)->comment('未发送');
            $table->json('sent_uid')->nullable()->comment('已发送员工ID');
            $table->json('not_sent_uid')->nullable()->comment('未发送员工ID');
            $table->string('msg_id')->default('')->comment('群发消息ID');
            $table->json('fail_list')->nullable()->comment('无效或无法发送的external_userid或chatid列表');
            $table->unsignedInteger('status')->default(1)->comment('状态:0、关闭,1、开启,2、执行中,3、完成;');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging');
    }
};
