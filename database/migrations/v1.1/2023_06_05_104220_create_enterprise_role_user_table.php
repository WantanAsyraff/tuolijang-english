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
        Schema::create('enterprise_role_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('role_id')->default(0)->index()->comment('角色(enterprise_role主键)iD');
            $table->integer('user_id')->default(0)->index()->comment('用户关联企业表(user_enterprise主键)ID');
            $table->tinyInteger('status')->default(0)->comment('状态1=开启;0=关闭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_role_user');
    }
};
