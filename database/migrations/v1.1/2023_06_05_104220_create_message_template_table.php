<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_template', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('relation_id')->default(0)->comment('总平台ID');
            $table->integer('message_id')->default(0)->comment('系统消息id');
            $table->tinyInteger('type')->default(0)->comment('类型:0=系统消息;1=短信消息;2=企微bot；3=钉钉机器人;4=其他bot');
            $table->string('template_id', 10)->default('')->comment('模板id,可以为短信模板');
            $table->string('message_title', 100)->default('')->comment('消息标题');
            $table->string('image')->default('')->comment('消息图片');
            $table->string('url')->default('')->comment('跳转标题');
            $table->string('uni_url', 256)->nullable()->default('')->comment('移动端跳转链接');
            $table->tinyInteger('status')->default(0)->comment('开启状态:0=关闭;1=开启');
            $table->boolean('relation_status')->default(false)->comment('系统消息状态');
            $table->string('content_template', 500)->default('')->comment('内容模板');
            $table->string('button_template')->default('')->comment('按钮模板');
            $table->tinyInteger('push_rule')->default(0)->comment('推送规则:0=即时推送;1=延迟推送');
            $table->integer('minute')->default(0)->comment('几分钟后推送');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['message_id', 'type'], 'message_id_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_template');
    }
};
