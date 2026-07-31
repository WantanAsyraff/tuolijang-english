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
        Schema::create('enterprise_message_notice', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_message_notice_entid_foreign');
            $table->integer('send_id')->default(0)->comment('发送人或者企业ID');
            $table->char('to_uid', 36)->index('eb_enterprise_message_notice_to_uid_foreign');
            $table->string('url')->default('')->comment('跳转链接');
            $table->string('uni_url', 128)->default('')->comment('uni跳转路径');
            $table->string('image')->nullable()->comment('图片');
            $table->string('title')->default('')->comment('消息标题');
            $table->string('message', 500)->default('')->comment('消息内容');
            $table->tinyInteger('type')->default(0)->comment('消息类型:1=系统消息;0=个人消息;3=企业站内消息');
            $table->integer('cate_id')->default(0)->comment('消息类型');
            $table->unsignedInteger('message_id')->default(0)->comment('消息模板ID');
            $table->string('cate_name')->default('');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读:1=已读;0=未读');
            $table->boolean('is_handle')->default(false)->comment('是否已处理');
            $table->unsignedTinyInteger('is_show')->default(1)->comment('是否显示');
            $table->string('template_type', 128)->index('template_type')->comment('消息类型');
            $table->string('button_template', 256)->default('')->comment('消息类型');
            $table->text('other')->nullable()->comment('其他附加消息内容');
            $table->unsignedInteger('link_id')->default(0)->comment('关联记录ID');
            $table->integer('link_status')->default(0)->comment('关联记录状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_message_notice');
    }
};
