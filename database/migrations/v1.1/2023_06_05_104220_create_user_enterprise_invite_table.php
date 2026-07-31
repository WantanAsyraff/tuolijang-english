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
        Schema::create('user_enterprise_invite', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->string('send_uid', 50)->default('')->comment('生成邀请码用户uuid');
            $table->integer('frame_id')->default(0)->comment('组织架构ID');
            $table->tinyInteger('is_verify')->default(0)->comment('是否需要企业审核：1、是；0、否；');
            $table->string('uniqued', 32)->default('')->comment('链接唯一值');
            $table->string('perfect_key', 32)->nullable()->comment('邀请完善信息记录标识');
            $table->timestamp('fail_time')->nullable()->comment('失效时间');
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
        Schema::dropIfExists('user_enterprise_invite');
    }
};
