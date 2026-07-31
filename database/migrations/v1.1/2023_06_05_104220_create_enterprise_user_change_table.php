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
        Schema::create('enterprise_user_change', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedInteger('card_id')->default(0)->comment('企业用户名片ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('变动类型：0、入职；1、转正；2、调岗；3、离职；');
            $table->date('date')->nullable()->comment('变动时间');
            $table->unsignedInteger('new_frame')->default(0)->comment('新部门ID');
            $table->unsignedInteger('old_frame')->default(0)->comment('原部门ID');
            $table->unsignedInteger('new_position')->default(0)->comment('新职位ID');
            $table->unsignedInteger('old_position')->default(0)->comment('原职位ID');
            $table->string('info', 128)->default('')->comment('原因说明');
            $table->string('mark', 500)->default('')->comment('备注信息');
            $table->unsignedInteger('link_id')->default(0)->comment('关联申请单ID');
            $table->unsignedInteger('user_id')->default(0)->comment('转移人员ID');
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
        Schema::dropIfExists('enterprise_user_change');
    }
};
