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
        Schema::create('enterprise_notice', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('cate_id')->default(0)->comment('分类ID');
            $table->unsignedBigInteger('card_id')->index('eb_enterprise_notice_card_id_foreign');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_notice_entid_foreign');
            $table->string('title', 64)->default('')->comment('通知标题');
            $table->string('cover', 256)->default('')->comment('封面图');
            $table->string('info', 256)->default('')->comment('通知简介');
            $table->text('content')->comment('内容详情');
            $table->unsignedTinyInteger('is_top')->default(1)->comment('是否置顶');
            $table->unsignedTinyInteger('push_type')->default(0)->comment('发布类型：0、立即；1、定时；');
            $table->timestamp('push_time')->comment('发布时间');
            $table->unsignedTinyInteger('status')->default(1)->comment('是否显示');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedInteger('visit')->default(0)->comment('浏览量');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entid'], 'entid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_notice');
    }
};
