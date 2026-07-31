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
        Schema::create('user_pending', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->char('uid', 36)->index('eb_user_pending_uid_foreign');
            $table->unsignedBigInteger('entid')->index('eb_user_pending_entid_foreign');
            $table->tinyInteger('type')->default(0)->comment('待办类型:1=绩效;2=日报');
            $table->string('url')->default('')->comment('跳转路径');
            $table->string('content')->default('')->comment('待办内容');
            $table->timestamp('pend_ent_time')->nullable()->comment('待办事件结束时间');
            $table->tinyInteger('status')->default(0)->comment('状态:1=已处理;0=未处理');
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
        Schema::dropIfExists('user_pending');
    }
};
