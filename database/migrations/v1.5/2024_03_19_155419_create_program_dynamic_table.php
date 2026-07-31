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
        Schema::create('program_dynamic', function (Blueprint $table) {
            $table->id()->comment('自增id');
            $table->tinyInteger('types')->default(0)->comment('动态类型 1：项目；2：任务；');
            $table->bigInteger('uid')->comment('操作人ID');
            $table->string('operator', 30)->default('')->comment('操作人姓名');
            $table->bigInteger('relation_id')->comment('操作ID');
            $table->tinyInteger('action_type')->default(0)->comment('动作类型 1：创建；2：修改；');
            $table->text('title')->default('')->comment('操作说明');
            $table->text('describe')->default('')->comment('描述');
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
        Schema::dropIfExists('program_dynamic');
    }
};
