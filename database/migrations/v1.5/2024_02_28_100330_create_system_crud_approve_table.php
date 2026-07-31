<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_crud_approve', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->unsignedBigInteger('user_id')->default(0)->comment('创建用户ID');
            $table->string('name', 255)->default('')->comment('审批名称');
            $table->string('icon', 256)->default('')->comment('审批图标');
            $table->string('color', 32)->default('')->comment('审批图标颜色');
            $table->text('info')->comment('审批说明');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0、关闭；1、开启；');
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
        Schema::dropIfExists('system_crud_approve');
    }
};
