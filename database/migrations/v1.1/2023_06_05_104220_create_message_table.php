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
        Schema::create('message', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业id');
            $table->integer('relation_id')->default(0)->comment('总平台ID');
            $table->integer('cate_id')->default(0)->comment('类型ID');
            $table->string('cate_name', 50)->default('');
            $table->string('template_type', 60)->default('')->index('template_type')->comment('关联通知类型');
            $table->string('template_var', 5000)->nullable();
            $table->boolean('template_time')->default(false);
            $table->string('title', 100)->default('')->comment('消息标题');
            $table->string('content', 500)->default('')->comment('消息内容');
            $table->string('remind_time', 20)->default('')->comment('提醒时间');
            $table->unsignedTinyInteger('user_sub')->default(0)->comment('用户可取消订阅');
            $table->softDeletes();
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
        Schema::dropIfExists('message');
    }
};
