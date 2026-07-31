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
        Schema::create('client_subscribe', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->index('eb_enterprise_client_subscribe_entid_index')->comment('企业ID');
            $table->integer('uid')->index('eb_enterprise_client_subscribe_uid_index')->comment('用户ID');
            $table->integer('eid')->index('eb_enterprise_client_subscribe_eid_index')->comment('关联客户ID');
            $table->tinyInteger('subscribe_status')->default(0)->comment('关注状态：0、取消关注；1、已关注；');
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
        Schema::dropIfExists('client_subscribe');
    }
};
