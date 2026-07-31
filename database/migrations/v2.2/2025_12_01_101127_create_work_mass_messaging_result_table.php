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
        Schema::create('work_mass_messaging_result', function (Blueprint $table) {
            $table->comment('企微消息群发结果表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('task_id')->default(0)->comment('群发任务ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('用户ID');
            $table->string('chat_id', 32)->default('')->comment('客户群ID');
            $table->string('external_userid', 32)->default('')->comment('客户ID');
            $table->string('userid', 32)->default('')->comment('员工ID');
            $table->unsignedInteger('status')->default(1)->comment('发送状态：0-未发送 1-已发送 2-因客户不是好友导致发送失败 3-因客户已经收到其他群发消息导致发送失败');
            $table->timestamp('send_time')->nullable()->comment('发送时间');
            $table->timestamps();
            $table->tinyInteger('is_comment')->nullable()->default(0);
            $table->tinyInteger('is_like')->nullable()->default(0);
            $table->string('msgid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging_result');
    }
};
