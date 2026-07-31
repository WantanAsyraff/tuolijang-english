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
        Schema::create('user_enterprise_apply', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('发送人或者企业ID');
            $table->string('send_uid', 50)->default('');
            $table->string('uid', 36)->default('')->comment('送达人id或者企业');
            $table->integer('frame_id');
            $table->integer('status')->default(0)->comment('-1=待处理,0=拒绝;1=同意');
            $table->boolean('verify')->default(false)->comment('审核状态：0、待审核；1、已通过；-1、拒绝；');
            $table->string('perfect_key', 32)->nullable()->comment('邀请完善信息记录关联');
            $table->timestamp('created_at')->nullable()->comment('申请时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_enterprise_apply');
    }
};
