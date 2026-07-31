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
        Schema::create('approve_apply', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedInteger('card_id')->default(0)->comment('创建用户名片ID');
            $table->unsignedBigInteger('approve_id')->index('eb_enterprise_approve_apply_approve_id_foreign');
            $table->string('node_id', 32)->default('')->comment('当前节点ID');
            $table->tinyInteger('status')->default(0)->comment('申请状态：-1、撤回；0、待审批；1、已通过；2、已拒绝；');
            $table->text('info')->comment('说明');
            $table->string('number', 32)->default('')->comment('编号');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_apply');
    }
};
