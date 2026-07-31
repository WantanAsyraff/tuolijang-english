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
        Schema::create('client_list', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid')->default('')->comment('创建用户ID');
            $table->integer('cid')->nullable()->default(0)->comment('分类ID');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->string('client_no', 15)->default('')->comment('客户编号');
            $table->string('name', 36)->default('')->comment('客户名称');
            $table->string('phone')->default('')->comment('联系电话');
            $table->string('email')->default('')->comment('客户邮箱');
            $table->string('label')->default('')->comment('客户邮箱');
            $table->string('source')->default('')->comment('客户来源');
            $table->string('address')->default('')->comment('客户地址');
            $table->string('detail')->default('')->comment('地址详情');
            $table->unsignedTinyInteger('follow')->default(0)->comment('是否关注');
            $table->unsignedTinyInteger('up_follow')->default(0)->comment('上级是否关注');
            $table->unsignedTinyInteger('status')->default(0)->comment('成交状态：0、未成交；1、已成交；');
            $table->string('creator', 36)->nullable()->comment('创建人ID');
            $table->text('mark')->comment('备注信息');
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
        Schema::dropIfExists('client_list');
    }
};
