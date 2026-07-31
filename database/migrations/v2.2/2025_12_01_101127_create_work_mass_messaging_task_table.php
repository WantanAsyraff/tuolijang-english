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
        Schema::create('work_mass_messaging_task', function (Blueprint $table) {
            $table->comment('企微群发成员发送任务表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('mass_id')->default(0)->comment('群发任务ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('用户ID');
            $table->string('userid', 256)->default('')->comment('员工ID');
            $table->string('msgid', 256)->default('')->comment('群发消息的id');
            $table->unsignedInteger('status')->default(0)->comment('发送状态：0-未发送 2-已发送');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型:1、群聊消息,0、群发消息,2、朋友圈消息;');
            $table->json('fail_list')->nullable()->comment('无效或无法发送的external_userid或chatid列表');
            $table->timestamp('send_time')->nullable()->comment('发送时间');
            $table->timestamps();
            $table->string('jobid')->nullable();
            $table->string('moment_id')->nullable();
            $table->integer('sum_count')->nullable()->default(0);
            $table->integer('not_send_count')->nullable()->default(0);
            $table->integer('success_count')->nullable()->default(0);
            $table->integer('fail_count')->nullable()->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging_task');
    }
};
